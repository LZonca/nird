<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Element;
use Illuminate\Support\Facades\DB;

class BaseUpgrade extends Component
{
    public $elements = [];
    public $userFunds;
    public $baseId;
    public $canUpgradeThisTurn = true;
    public $currentYear;

    public function mount()
    {
        $user = auth()->user();
        $this->userFunds = $user->funds ?? 0;
        $this->baseId = $user->base_id;
        $this->currentYear = $user->year ?? 1;

        // Vérifier si l'utilisateur peut encore améliorer ce tour
        $this->canUpgradeThisTurn = ($user->last_upgrade_year ?? 0) < $this->currentYear;

        \Log::info('🏗️ BaseUpgrade mount:', [
            'year' => $this->currentYear,
            'last_upgrade_year' => $user->last_upgrade_year,
            'can_upgrade' => $this->canUpgradeThisTurn
        ]);

        // Charger tous les éléments avec leur niveau actuel pour cette base
        $this->loadElements();
    }

    public function initializeElement($elementId)
    {
        $user = auth()->user();

        if (!$user->base_id) {
            session()->flash('error', 'Vous n\'avez pas de base !');
            return;
        }

        $element = Element::find($elementId);
        if (!$element) {
            session()->flash('error', 'Élément introuvable !');
            return;
        }

        // Vérifier si l'élément n'est pas déjà initialisé
        $exists = DB::table('base_element')
            ->where('base_id', $user->base_id)
            ->where('element_id', $elementId)
            ->exists();

        if ($exists) {
            session()->flash('error', $element->name . ' est déjà initialisé !');
            return;
        }

        // Initialiser l'élément au niveau 1
        DB::table('base_element')->insert([
            'base_id' => $user->base_id,
            'element_id' => $elementId,
            'level' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        \Log::info('✅ Élément initialisé:', [
            'element' => $element->name,
            'base_id' => $user->base_id
        ]);

        session()->flash('success', $element->name . ' initialisé au niveau 1 !');
        $this->loadElements();
    }

    public function loadElements()
    {
        $user = auth()->user();

        if (!$user->base_id) {
            $this->elements = [];
            return;
        }

        // Récupérer tous les éléments avec leur niveau actuel pour cette base
        $this->elements = Element::leftJoin('base_element', function($join) use ($user) {
                $join->on('elements.id', '=', 'base_element.element_id')
                     ->where('base_element.base_id', '=', $user->base_id);
            })
            ->select('elements.*', DB::raw('COALESCE(base_element.level, 0) as current_level'))
            ->get()
            ->map(function($element) {
                $currentLevel = (int) $element->current_level;
                $isInitialized = $currentLevel > 0; // Si level > 0, c'est initialisé

                // Calculer le coût réel pour passer au niveau suivant : coût_base * (1.1 ^ (niveau_actuel - 1))
                $realUpgradeCost = $isInitialized ? ceil($element->upgrade_cost * pow(1.1, $currentLevel - 1)) : $element->upgrade_cost;

                return [
                    'id' => $element->id,
                    'name' => $element->name,
                    'url' => $element->url,
                    'level_max' => $element->level_max,
                    'upgrade_cost' => $realUpgradeCost,
                    'base_cost' => $element->upgrade_cost,
                    'current_level' => $currentLevel,
                    'is_initialized' => $isInitialized,
                    'can_upgrade' => $currentLevel < $element->level_max && $isInitialized,
                    'is_tree' => str_contains(strtolower($element->name), 'arbre') || str_contains(strtolower($element->name), 'tree')
                ];
            })
            ->toArray();

        \Log::info('🏗️ Éléments chargés:', [
            'count' => count($this->elements),
            'initialized' => count(array_filter($this->elements, fn($e) => $e['is_initialized'])),
            'not_initialized' => count(array_filter($this->elements, fn($e) => !$e['is_initialized'])),
            'elements_details' => array_map(fn($e) => [
                'id' => $e['id'],
                'name' => $e['name'],
                'level' => $e['current_level'],
                'is_init' => $e['is_initialized']
            ], $this->elements)
        ]);
    }

    public function upgradeElement($elementId)
    {
        $user = auth()->user();

        if (!$user->base_id) {
            session()->flash('error', 'Vous n\'avez pas de base !');
            return;
        }

        // Vérifier si l'utilisateur peut encore améliorer ce tour
        if (($user->last_upgrade_year ?? 0) >= ($user->year ?? 1)) {
            session()->flash('error', '⏸️ Vous avez déjà effectué une amélioration cette année ! Revenez quand celle-ci sera terminée..');
            return;
        }

        $element = Element::find($elementId);
        if (!$element) {
            session()->flash('error', 'Élément introuvable !');
            return;
        }

        // Récupérer le niveau actuel
        $pivot = DB::table('base_element')
            ->where('base_id', $user->base_id)
            ->where('element_id', $elementId)
            ->first();

        $currentLevel = $pivot ? $pivot->level : 0;

        // Vérifications
        if ($currentLevel >= $element->level_max) {
            session()->flash('error', 'Niveau maximum atteint !');
            return;
        }

        // Calculer le coût réel pour ce niveau : coût de base * (1.1 ^ (niveau actuel - 1))
        // Niveau 1 → niveau 2 : coût de base * 1.1^0 = coût de base
        // Niveau 2 → niveau 3 : coût de base * 1.1^1 = coût de base * 1.1
        // Niveau 3 → niveau 4 : coût de base * 1.1^2 = coût de base * 1.21
        $realCost = ceil($element->upgrade_cost * pow(1.1, $currentLevel - 1));

        \Log::info('💰 Calcul du coût:', [
            'element' => $element->name,
            'niveau_actuel' => $currentLevel,
            'cout_base' => $element->upgrade_cost,
            'cout_reel' => $realCost,
            'funds_user' => $user->funds
        ]);

        if ($user->funds < $realCost) {
            session()->flash('error', 'Pas assez de graines ! (' . $realCost . ' requis, vous avez ' . $user->funds . ')');
            return;
        }

        // Effectuer l'upgrade
        DB::beginTransaction();
        try {
            // Déduire les funds et marquer le tour
            $user->funds -= $realCost;
            $user->last_upgrade_year = $user->year ?? 1;
            $user->save();

            // Mettre à jour ou créer le niveau
            if ($pivot) {
                DB::table('base_element')
                    ->where('base_id', $user->base_id)
                    ->where('element_id', $elementId)
                    ->update(['level' => $currentLevel + 1, 'updated_at' => now()]);
            } else {
                DB::table('base_element')->insert([
                    'base_id' => $user->base_id,
                    'element_id' => $elementId,
                    'level' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            DB::commit();

            \Log::info('✅ Élément amélioré:', [
                'element' => $element->name,
                'ancien_niveau' => $currentLevel,
                'nouveau_niveau' => $currentLevel + 1,
                'cout_paye' => $realCost,
                'funds_restants' => $user->funds
            ]);

            session()->flash('success', $element->name . ' amélioré au niveau ' . ($currentLevel + 1) . ' ! (-' . $realCost . ' graines)');

            // Recharger les données
            $this->userFunds = $user->funds;
            $this->canUpgradeThisTurn = false; // Plus d'amélioration ce tour
            $this->loadElements();

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('❌ Erreur upgrade:', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de l\'amélioration !');
        }
    }

    public function render()
    {
        return view('livewire.base-upgrade')
            ->title('Amélioration de la Base');
    }
}
