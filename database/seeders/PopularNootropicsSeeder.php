<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PopularNootropicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nootropics = [
            [
                'name' => 'Modafinil',
                'description' => 'Wakefulness-promoting agent that enhances alertness and focus without the jittery side effects of traditional stimulants.',
                'category' => 'nootropic',
                'common_dosage' => '100-200mg',
                'notes' => 'Prescription medication. Best taken in the morning. Can improve focus for 8-12 hours.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'L-Theanine',
                'description' => 'Amino acid found in tea leaves that promotes relaxation without drowsiness. Often stacked with caffeine.',
                'category' => 'supplement',
                'common_dosage' => '100-400mg',
                'notes' => 'Naturally occurring in green tea. Synergizes well with caffeine to reduce jitters.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Caffeine',
                'description' => 'The world\'s most popular stimulant. Blocks adenosine receptors to reduce fatigue and increase alertness.',
                'category' => 'supplement',
                'common_dosage' => '50-200mg',
                'notes' => 'Found in coffee, tea, and energy drinks. Can cause jitters and crash if overdosed.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Piracetam',
                'description' => 'The original nootropic. Enhances memory and learning by modulating AMPA receptors.',
                'category' => 'nootropic',
                'common_dosage' => '1600-4800mg',
                'notes' => 'First synthetic nootropic discovered. Works best when stacked with choline sources.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Alpha-GPC',
                'description' => 'Choline source that crosses the blood-brain barrier efficiently. Supports acetylcholine production.',
                'category' => 'supplement',
                'common_dosage' => '300-600mg',
                'notes' => 'Excellent choline source for nootropic stacks. Supports memory and focus.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Bacopa Monnieri',
                'description' => 'Ayurvedic herb that enhances memory formation and reduces anxiety over time.',
                'category' => 'herb',
                'common_dosage' => '300-600mg',
                'notes' => 'Effects build over 8-12 weeks. Best taken with food. Standardized to 50% bacosides.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Lion\'s Mane Mushroom',
                'description' => 'Medicinal mushroom that supports nerve growth factor and cognitive function.',
                'category' => 'supplement',
                'common_dosage' => '500-1000mg',
                'notes' => 'Promotes neurogenesis. Effects build over time. Good for long-term brain health.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Rhodiola Rosea',
                'description' => 'Adaptogenic herb that helps manage stress and fatigue while supporting mental performance.',
                'category' => 'herb',
                'common_dosage' => '200-400mg',
                'notes' => 'Best taken on empty stomach. Standardized to 3% rosavins and 1% salidroside.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Phenylpiracetam',
                'description' => 'More potent version of piracetam with additional stimulating effects. Enhances focus and physical performance.',
                'category' => 'nootropic',
                'common_dosage' => '100-200mg',
                'notes' => 'More stimulating than piracetam. Cycle to avoid tolerance. Not for daily use.',
                'is_predefined' => true,
                'is_public' => true,
            ],
            [
                'name' => 'Noopept',
                'description' => 'Peptide nootropic that\'s much more potent than piracetam. Enhances focus and memory.',
                'category' => 'nootropic',
                'common_dosage' => '10-30mg',
                'notes' => 'Very potent - start with lower doses. Sublingual absorption is more effective.',
                'is_predefined' => true,
                'is_public' => true,
            ],
        ];

        foreach ($nootropics as $nootropic) {
            \App\Models\Substance::firstOrCreate(
                ['name' => $nootropic['name']],
                $nootropic
            );
        }
    }
}
