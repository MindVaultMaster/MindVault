<?php

namespace Database\Seeders;

use App\Models\Substance;
use App\Models\SubstanceResource;
use Illuminate\Database\Seeder;

class ResearchLibrarySeeder extends Seeder
{
    public function run(): void
    {
        // Get substances by name for reference
        $modafinil = Substance::where('name', 'Modafinil')->first();
        $ltheanine = Substance::where('name', 'L-Theanine')->first();
        $caffeine = Substance::where('name', 'Caffeine')->first();
        $piracetam = Substance::where('name', 'Piracetam')->first();
        $bacopa = Substance::where('name', 'Bacopa Monnieri')->first();

        $research = [
            // Modafinil Research
            [
                'substance_id' => $modafinil?->id,
                'title' => 'Modafinil for cognitive neuroenhancement in healthy non-sleep-deprived subjects: a systematic review',
                'description' => 'Systematic review examining modafinil\'s cognitive enhancing effects in healthy individuals.',
                'type' => 'review',
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/25875312/',
                'authors' => 'Battleday RM, Brem AK',
                'publication' => 'European Neuropsychopharmacology',
                'publication_date' => '2015-10-01',
                'doi' => '10.1016/j.euroneuro.2015.07.028',
                'abstract' => 'This systematic review found that modafinil enhances cognition, specifically executive functions and attention, in healthy non-sleep-deprived individuals.',
                'tags' => ['cognitive enhancement', 'executive function', 'attention', 'healthy subjects'],
                'quality_score' => 8,
                'key_findings' => 'Modafinil consistently enhances cognition, particularly executive functions and attention, in healthy individuals. Effects are most pronounced on complex tasks.',
                'is_verified' => true,
            ],

            // L-Theanine Research
            [
                'substance_id' => $ltheanine?->id,
                'title' => 'L-theanine, a natural constituent in tea, and its effect on mental state',
                'description' => 'Review of L-theanine\'s effects on relaxation, stress, and cognitive performance.',
                'type' => 'review',
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/18296328/',
                'authors' => 'Nobre AC, Rao A, Owen GN',
                'publication' => 'Asia Pacific Journal of Clinical Nutrition',
                'publication_date' => '2008-01-01',
                'doi' => '10.6133/apjcn.2008.17.s1.67',
                'abstract' => 'L-theanine promotes relaxation without drowsiness and may improve focus and attention when combined with caffeine.',
                'tags' => ['relaxation', 'stress reduction', 'focus', 'caffeine synergy'],
                'quality_score' => 7,
                'key_findings' => 'L-theanine promotes alpha brain wave activity associated with relaxation. Synergistic effects with caffeine improve both focus and calmness.',
                'is_verified' => true,
            ],

            // Caffeine + L-Theanine Study
            [
                'substance_id' => $caffeine?->id,
                'title' => 'The combination of L-theanine and caffeine improves cognitive performance and increases subjective alertness',
                'description' => 'Study examining the synergistic effects of L-theanine and caffeine on cognitive performance.',
                'type' => 'study',
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/18681988/',
                'authors' => 'Owen GN, Parnell H, De Bruin EA, Rycroft JA',
                'publication' => 'Nutritional Neuroscience',
                'publication_date' => '2008-08-01',
                'doi' => '10.1179/147683008X301513',
                'abstract' => 'The combination of L-theanine (97mg) and caffeine (40mg) improved both speed and accuracy of performance and reduced susceptibility to distracting information.',
                'tags' => ['cognitive performance', 'attention', 'alertness', 'combination'],
                'quality_score' => 8,
                'key_findings' => 'L-theanine + caffeine combination improves attention switching, reduces mind-wandering, and increases alertness without jitters.',
                'is_verified' => true,
            ],

            // Piracetam Research
            [
                'substance_id' => $piracetam?->id,
                'title' => 'Piracetam: a review of pharmacological properties and clinical uses',
                'description' => 'Comprehensive review of piracetam\'s mechanisms and clinical applications.',
                'type' => 'review',
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/7301036/',
                'authors' => 'Winblad B',
                'publication' => 'CNS Drug Reviews',
                'publication_date' => '2005-01-01',
                'doi' => '10.1111/j.1527-3458.2005.tb00268.x',
                'abstract' => 'Piracetam enhances learning and memory through modulation of AMPA receptors and improved neuronal plasticity.',
                'tags' => ['memory', 'learning', 'neuroprotection', 'AMPA receptors'],
                'quality_score' => 7,
                'key_findings' => 'Piracetam enhances memory formation and retrieval, particularly in age-related cognitive decline. Requires consistent dosing for optimal effects.',
                'is_verified' => true,
            ],

            // Bacopa Research
            [
                'substance_id' => $bacopa?->id,
                'title' => 'Chronic effects of Brahmi (Bacopa monnieri) on human memory',
                'description' => 'Double-blind study examining Bacopa\'s effects on memory in healthy adults.',
                'type' => 'study',
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/12093601/',
                'authors' => 'Roodenrys S, Booth D, Bulzomi S, Phipps A, Micallef C, Smoker J',
                'publication' => 'Neuropsychopharmacology',
                'publication_date' => '2002-08-01',
                'doi' => '10.1016/S0893-133X(02)00284-1',
                'abstract' => '12-week study showing Bacopa significantly improved speed of visual information processing, learning rate and memory consolidation.',
                'tags' => ['memory consolidation', 'learning', 'information processing', 'chronic effects'],
                'quality_score' => 8,
                'key_findings' => 'Bacopa improves memory formation and retention after 12 weeks of use. Effects are cumulative and most pronounced with long-term use.',
                'is_verified' => true,
            ],

            // Safety Studies
            [
                'substance_id' => $modafinil?->id,
                'title' => 'Safety and tolerability of modafinil: a systematic review',
                'description' => 'Comprehensive safety analysis of modafinil use in various populations.',
                'type' => 'review',
                'url' => 'https://pubmed.ncbi.nlm.nih.gov/30927272/',
                'authors' => 'Perez M, et al.',
                'publication' => 'Drug Safety',
                'publication_date' => '2019-03-01',
                'abstract' => 'Modafinil generally well-tolerated with low abuse potential. Most common side effects include headache, nausea, and anxiety.',
                'tags' => ['safety', 'tolerability', 'side effects', 'abuse potential'],
                'quality_score' => 7,
                'key_findings' => 'Modafinil has good safety profile in healthy individuals. Low risk of dependence compared to traditional stimulants.',
                'is_verified' => true,
            ],
        ];

        foreach ($research as $item) {
            if ($item['substance_id']) {
                SubstanceResource::create($item);
            }
        }
    }
}
