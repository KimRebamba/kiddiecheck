<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EccdSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            [
                'name' => 'Gross Motor',
                'description' => 'Gross motor development (13 items)',
                'items' => [
                    ['text' => 'Climbs on chair or other elevated furniture without help', 'type' => 'static', 'instructions' => 'Observe child attempting to climb safely.', 'materials' => 'Chair or low bed', 'procedure' => 'Invite child to climb up and down with supervision.'],
                    ['text' => 'Walks backwards', 'type' => 'static', 'instructions' => 'Ask child to walk backwards a few steps.', 'materials' => 'Open space', 'procedure' => 'Demonstrate, then observe independent walking backwards.'],
                    ['text' => 'Runs without tripping or falling', 'type' => 'static', 'instructions' => 'Ask child to run a short distance.', 'materials' => 'Open space', 'procedure' => 'Observe natural running pace over clear area.'],
                    ['text' => 'Walks down stairs, two feet on each step, with one hand held', 'type' => 'static', 'instructions' => 'Assist child down stairs with hand held.', 'materials' => 'Stairs with railing', 'procedure' => 'Provide support and observe stepping pattern.'],
                    ['text' => 'Walks upstairs holding onto a handrail, two feet on each step', 'type' => 'static', 'instructions' => 'Ask child to ascend with handrail.', 'materials' => 'Stairs with railing', 'procedure' => 'Observe stepping pattern (two feet per step).'],
                    ['text' => 'Walks upstairs with alternate feet without holding onto a handrail', 'type' => 'static', 'instructions' => 'Ask child to ascend without handrail.', 'materials' => 'Stairs', 'procedure' => 'Observe alternating feet each step.'],
                    ['text' => 'Walks downstairs with alternate feet without holding onto a handrail', 'type' => 'static', 'instructions' => 'Ask child to descend without handrail.', 'materials' => 'Stairs', 'procedure' => 'Observe alternating feet each step.'],
                    ['text' => 'Moves body part as directed', 'type' => 'static', 'instructions' => 'Give simple movement commands.', 'materials' => 'None', 'procedure' => 'Ask child to move arms, legs, head on cue.'],
                    ['text' => 'Jumps up', 'type' => 'static', 'instructions' => 'Ask child to jump in place.', 'materials' => 'Open space', 'procedure' => 'Observe vertical jump; note balance and take-off.'],
                    ['text' => 'Throws ball overhead with direction', 'type' => 'static', 'instructions' => 'Ask child to throw ball overhead toward target.', 'materials' => 'Soft ball; target', 'procedure' => 'Observe overhand throw aiming toward target.'],
                    ['text' => 'Hops one to three steps on preferred foot', 'type' => 'static', 'instructions' => 'Ask child to hop on one foot.', 'materials' => 'Open space', 'procedure' => 'Observe up to three consecutive hops on same foot.'],
                    ['text' => 'Jumps and turns', 'type' => 'static', 'instructions' => 'Ask child to jump and rotate body.', 'materials' => 'Open space', 'procedure' => 'Observe jump with turn (any direction).'],
                    ['text' => 'Dances patterns/joins group movement activities', 'type' => 'static', 'instructions' => 'Invite child to follow simple dance pattern.', 'materials' => 'Music (optional)', 'procedure' => 'Lead a short routine; observe participation and coordination.'],
                ],
            ],
            [
                'name' => 'Fine Motor',
                'description' => 'Fine motor development (11 items)',
                'items' => [
                    ['text' => 'Uses all five fingers to get food/toys on a flat surface', 'type' => 'static', 'instructions' => 'Place small items on table.', 'materials' => 'Small toys or snacks', 'procedure' => 'Observe grasp using all fingers to retrieve items.'],
                    ['text' => 'Picks up objects with thumb and index finger', 'type' => 'static', 'instructions' => 'Present small bead or raisin.', 'materials' => 'Beads/raisins', 'procedure' => 'Observe pincer grasp.'],
                    ['text' => 'Displays a definite hand preference', 'type' => 'static', 'instructions' => 'Offer tasks that require reaching/grasping.', 'materials' => 'Common objects', 'procedure' => 'Note consistent use of left/right hand across tasks.'],
                    ['text' => 'Puts small objects in/out of containers', 'type' => 'static', 'instructions' => 'Provide container and small items.', 'materials' => 'Cup/box and small objects', 'procedure' => 'Observe placing and removing items.'],
                    ['text' => 'Holds crayon with all fingers making a fist (palmar grasp)', 'type' => 'static', 'instructions' => 'Give crayon to child.', 'materials' => 'Crayon and paper', 'procedure' => 'Observe grasp style and scribbling.'],
                    ['text' => 'Unscrews lid of a container or unwraps food', 'type' => 'static', 'instructions' => 'Provide jar with loose lid.', 'materials' => 'Jar or wrapped snack', 'procedure' => 'Observe unscrewing or unwrapping.'],
                    ['text' => 'Scribbles spontaneously', 'type' => 'static', 'instructions' => 'Offer paper and crayon.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe spontaneous scribbling.'],
                    ['text' => 'Scribbles vertical and horizontal lines', 'type' => 'static', 'instructions' => 'Prompt child to draw lines.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe vertical and horizontal strokes.'],
                    ['text' => 'Draws circle purposefully', 'type' => 'static', 'instructions' => 'Ask child to draw a circle.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe circular drawing.'],
                    ['text' => 'Draws a human figure (head, eyes, trunk, arms, hands/fingers)', 'type' => 'static', 'instructions' => 'Prompt child to draw a person.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe included parts in drawing.'],
                    ['text' => 'Draws a house using geometric forms', 'type' => 'static', 'instructions' => 'Ask child to draw a house.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe use of shapes (square, triangle).'],
                ],
            ],
            [
                'name' => 'Self-Help',
                'description' => 'Self-help skills (27 items)',
                'items' => [
                    ['text' => 'Feeds self with finger food using fingers', 'type' => 'static', 'instructions' => 'Offer finger foods.', 'materials' => 'Biscuits or bread', 'procedure' => 'Observe self-feeding and grasp.'],
                    ['text' => 'Feeds self using fingers to eat rice/viands with spillage', 'type' => 'static', 'instructions' => 'Provide small meal sample.', 'materials' => 'Rice/viands sample', 'procedure' => 'Observe self-feeding and spillage.'],
                    ['text' => 'Feeds self using spoon with spillage', 'type' => 'static', 'instructions' => 'Provide spoon and bowl.', 'materials' => 'Spoon, bowl with food', 'procedure' => 'Observe scooping and bringing to mouth.'],
                    ['text' => 'Feeds self using fingers without spillage', 'type' => 'static', 'instructions' => 'Offer finger foods.', 'materials' => 'Finger foods', 'procedure' => 'Observe minimal spillage.'],
                    ['text' => 'Feeds self using spoon without spillage', 'type' => 'static', 'instructions' => 'Provide spoon and bowl.', 'materials' => 'Spoon, bowl with food', 'procedure' => 'Observe control and no spillage.'],
                    ['text' => 'Eats without need for spoonfeeding during any meal', 'type' => 'static', 'instructions' => 'Observe typical meal behavior.', 'materials' => 'Usual meal', 'procedure' => 'Note independence throughout meal.'],
                    ['text' => 'Helps hold cup for drinking', 'type' => 'static', 'instructions' => 'Provide cup with water.', 'materials' => 'Cup with water', 'procedure' => 'Observe assisting in holding cup.'],
                    ['text' => 'Drinks from cup with spillage', 'type' => 'static', 'instructions' => 'Provide cup with water.', 'materials' => 'Cup with water', 'procedure' => 'Observe drinking and spillage.'],
                    ['text' => 'Drinks from cup unassisted', 'type' => 'static', 'instructions' => 'Provide cup with water.', 'materials' => 'Cup with water', 'procedure' => 'Observe unassisted drinking.'],
                    ['text' => 'Gets drink for self unassisted', 'type' => 'static', 'instructions' => 'Ask child to get a drink.', 'materials' => 'Accessible water source', 'procedure' => 'Observe initiation and completion.'],
                    ['text' => 'Pours from pitcher without spillage', 'type' => 'static', 'instructions' => 'Provide small pitcher and cup.', 'materials' => 'Pitcher and cup', 'procedure' => 'Observe pouring control.'],
                    ['text' => 'Prepares own food/snack', 'type' => 'static', 'instructions' => 'Invite child to make a simple snack.', 'materials' => 'Bread, spread, utensils', 'procedure' => 'Observe steps and safety.'],
                    ['text' => 'Prepares meals for younger siblings when no adult is around', 'type' => 'static', 'instructions' => 'Discuss/observe capability for simple meal prep.', 'materials' => 'Simple meal items', 'procedure' => 'Parental report or supervised demo.'],
                    ['text' => 'Participates when being dressed (raises arms or lifts leg)', 'type' => 'static', 'instructions' => 'Prompt help during dressing.', 'materials' => 'Clothing', 'procedure' => 'Observe cooperation with prompts.'],
                    ['text' => 'Pulls down gartered short pants', 'type' => 'static', 'instructions' => 'Ask child to pull down shorts.', 'materials' => 'Shorts', 'procedure' => 'Observe ability and coordination.'],
                    ['text' => 'Removes sando', 'type' => 'static', 'instructions' => 'Ask child to remove undershirt.', 'materials' => 'Sando/undershirt', 'procedure' => 'Observe steps and independence.'],
                    ['text' => 'Dresses without assistance except for buttons and tying', 'type' => 'static', 'instructions' => 'Provide clothing with simple fasteners.', 'materials' => 'Garments', 'procedure' => 'Observe dressing mostly independently.'],
                    ['text' => 'Dresses without assistance including buttons and tying', 'type' => 'static', 'instructions' => 'Provide clothing with buttons and laces.', 'materials' => 'Buttoned shirt, shoes with laces', 'procedure' => 'Observe full independence.'],
                    ['text' => 'Informs adult only after urinating/defecating in underpants', 'type' => 'static', 'instructions' => 'Parental report about toileting cues.', 'materials' => 'None', 'procedure' => 'Discuss recent events and patterns.'],
                    ['text' => 'Informs adult of need to urinate/defecate to be brought to designated place', 'type' => 'static', 'instructions' => 'Parental report about toileting cues.', 'materials' => 'None', 'procedure' => 'Discuss anticipation of toileting.'],
                    ['text' => 'Goes to designated place to urinate/defecate but sometimes still uses underpants', 'type' => 'static', 'instructions' => 'Parental report and observation.', 'materials' => 'Bathroom', 'procedure' => 'Note consistency and accidents.'],
                    ['text' => 'Goes to designated place to urinate/defecate and never uses underpants anymore', 'type' => 'static', 'instructions' => 'Parental report and observation.', 'materials' => 'Bathroom', 'procedure' => 'Confirm complete toilet training.'],
                    ['text' => 'Wipes/cleans self after bowel movement', 'type' => 'static', 'instructions' => 'Discuss and observe if appropriate.', 'materials' => 'Bathroom supplies', 'procedure' => 'Confirm ability and hygiene.'],
                    ['text' => 'Participates when bathing (e.g., rubbing arms with soap)', 'type' => 'static', 'instructions' => 'Observe child during bathing.', 'materials' => 'Soap and water', 'procedure' => 'Note engagement and technique.'],
                    ['text' => 'Washes and dries hands without any help', 'type' => 'static', 'instructions' => 'Ask child to wash hands.', 'materials' => 'Sink, soap, towel', 'procedure' => 'Observe full sequence independently.'],
                    ['text' => 'Washes face without any help', 'type' => 'static', 'instructions' => 'Ask child to wash face.', 'materials' => 'Sink, soap, towel', 'procedure' => 'Observe independence and thoroughness.'],
                    ['text' => 'Bathes without any help', 'type' => 'static', 'instructions' => 'Parental report; supervise if needed.', 'materials' => 'Bathing materials', 'procedure' => 'Confirm ability to bathe independently.'],
                ],
            ],
            [
                'name' => 'Receptive Language',
                'description' => 'Understanding language (5 items)',
                'items' => [
                    ['text' => 'Points to a family member when asked', 'type' => 'static', 'instructions' => 'Ask child to point to named family member.', 'materials' => 'Presence of family or photos', 'procedure' => 'Observe accurate pointing.'],
                    ['text' => 'Points to five body parts on himself when asked', 'type' => 'static', 'instructions' => 'Ask child to point to named body parts.', 'materials' => 'None', 'procedure' => 'Observe accurate pointing on self.'],
                    ['text' => 'Follows one-step instructions with simple prepositions', 'type' => 'static', 'instructions' => 'Give one-step commands (in, on, under).', 'materials' => 'Physical objects', 'procedure' => 'Observe compliance with prepositions.'],
                    ['text' => 'Follows two-step instructions with simple prepositions', 'type' => 'static', 'instructions' => 'Give two-step commands.', 'materials' => 'Physical objects', 'procedure' => 'Observe sequencing and prepositions.'],
                    ['text' => 'Points to five named pictured objects when asked to do so', 'type' => 'interactive', 'instructions' => 'Show picture board and ask child to point/click.', 'materials' => 'Picture cards or on-screen images', 'procedure' => 'Present images and record correct selections.'],
                ],
            ],
            [
                'name' => 'Expressive Language',
                'description' => 'Expressing language (8 items)',
                'items' => [
                    ['text' => 'Uses five to 20 recognizable words', 'type' => 'static', 'instructions' => 'Parental report; sample vocabulary.', 'materials' => 'None', 'procedure' => 'Ask caregiver to list words; validate in play.'],
                    ['text' => 'Uses pronouns (I, me, ako, akin)', 'type' => 'static', 'instructions' => 'Parental report; observe in conversation.', 'materials' => 'None', 'procedure' => 'Prompt dialogue and note pronoun use.'],
                    ['text' => 'Uses two- to three-word verb-noun combinations', 'type' => 'static', 'instructions' => 'Parental report; observe phrases.', 'materials' => 'None', 'procedure' => 'Elicit requests and note structure.'],
                    ['text' => 'Speaks in grammatically correct two- to three-word sentences', 'type' => 'static', 'instructions' => 'Parental report; observe sentences.', 'materials' => 'None', 'procedure' => 'Prompt storytelling and note grammar.'],
                    ['text' => 'Asks "what" questions', 'type' => 'static', 'instructions' => 'Parental report; observe questioning.', 'materials' => 'None', 'procedure' => 'Note spontaneous question forms.'],
                    ['text' => 'Asks "who" and "why" questions', 'type' => 'static', 'instructions' => 'Parental report; observe questioning.', 'materials' => 'None', 'procedure' => 'Note use of who/why prompts.'],
                    ['text' => 'Gives account of recent experiences in order using past tense', 'type' => 'static', 'instructions' => 'Prompt recall of an event.', 'materials' => 'None', 'procedure' => 'Observe sequence and tense usage.'],
                    ['text' => 'Names objects in pictures', 'type' => 'interactive', 'instructions' => 'Show images and ask child to name.', 'materials' => 'Picture cards or on-screen images', 'procedure' => 'Record accuracy in naming.'],
                ],
            ],
            [
                'name' => 'Cognitive',
                'description' => 'Cognitive skills (21 items)',
                'items' => [
                    ['text' => 'Looks in the direction of fallen object', 'type' => 'static', 'instructions' => 'Drop object and observe gaze.', 'materials' => 'Small object', 'procedure' => 'Drop near child and watch tracking.'],
                    ['text' => 'Looks for a partially hidden object', 'type' => 'static', 'instructions' => 'Hide object partially under cloth.', 'materials' => 'Object and cloth', 'procedure' => 'Observe search and retrieval.'],
                    ['text' => 'Imitates behavior just seen a few minutes earlier', 'type' => 'static', 'instructions' => 'Demonstrate simple action; observe later.', 'materials' => 'Simple props', 'procedure' => 'Assess delayed imitation.'],
                    ['text' => 'Offers an object but will not release it', 'type' => 'static', 'instructions' => 'Request object; observe reluctance.', 'materials' => 'Toy', 'procedure' => 'Note offering gesture without release.'],
                    ['text' => 'Looks for a completely hidden object', 'type' => 'static', 'instructions' => 'Hide object under cloth/box.', 'materials' => 'Object and cover', 'procedure' => 'Observe search behavior.'],
                    ['text' => 'Exhibits simple pretend play (feeds, puts doll to sleep)', 'type' => 'static', 'instructions' => 'Provide doll and props.', 'materials' => 'Doll, toy dishes/blanket', 'procedure' => 'Observe pretend actions.'],
                    ['text' => 'Matches objects', 'type' => 'static', 'instructions' => 'Provide pairs of similar objects.', 'materials' => 'Spoons, balls, blocks', 'procedure' => 'Observe matching by attribute.'],
                    ['text' => 'Sorts based on shapes', 'type' => 'static', 'instructions' => 'Provide shape sorter.', 'materials' => 'Shape pieces', 'procedure' => 'Observe sorting by shapes.'],
                    ['text' => 'Sorts objects based on two attributes (size and color)', 'type' => 'static', 'instructions' => 'Provide varied shapes by size and color.', 'materials' => 'Shapes of different colors/sizes', 'procedure' => 'Observe sorting using two attributes.'],
                    ['text' => 'Arranges objects according to size from smallest to biggest', 'type' => 'static', 'instructions' => 'Provide graduated sizes.', 'materials' => 'Stacking cups or blocks', 'procedure' => 'Observe correct size ordering.'],
                    ['text' => 'Copies shapes', 'type' => 'static', 'instructions' => 'Show shapes; ask child to copy.', 'materials' => 'Paper, pencil', 'procedure' => 'Observe accuracy in copying.'],
                    ['text' => 'Can assemble simple puzzles', 'type' => 'static', 'instructions' => 'Provide simple jigsaw/puzzle.', 'materials' => 'Simple puzzles', 'procedure' => 'Observe assembly strategy.'],
                    ['text' => 'Matches two to three colors', 'type' => 'interactive', 'instructions' => 'Present color tiles on screen or cards.', 'materials' => 'Color tiles/cards or on-screen', 'procedure' => 'Ask child to match same colors.'],
                    ['text' => 'Matches pictures', 'type' => 'interactive', 'instructions' => 'Show picture pairs; ask to match.', 'materials' => 'Picture cards or on-screen', 'procedure' => 'Record correct matches.'],
                    ['text' => 'Names four to six colors', 'type' => 'interactive', 'instructions' => 'Show colored swatches; ask child to name/select.', 'materials' => 'Color swatches or on-screen', 'procedure' => 'Record named or selected color labels.'],
                    ['text' => 'Names 3 animals or vegetables when asked', 'type' => 'interactive', 'instructions' => 'Show images; ask child to name/select.', 'materials' => 'Animal/vegetable images', 'procedure' => 'Record correct items named or chosen.'],
                    ['text' => 'States what common household items are used for', 'type' => 'interactive', 'instructions' => 'Show item images; ask function.', 'materials' => 'Household item images', 'procedure' => 'Record correct function responses.'],
                    ['text' => 'Demonstrates understanding of opposites by completing a statement', 'type' => 'interactive', 'instructions' => 'Present statements with images; ask child to select opposite.', 'materials' => 'Opposite pairs images or text', 'procedure' => 'Record correct completions.'],
                    ['text' => 'Points to left and right sides of body', 'type' => 'interactive', 'instructions' => 'Show body diagram; ask left/right.', 'materials' => 'Body diagram or on-screen', 'procedure' => 'Record correct side identification.'],
                    ['text' => 'Can state what is silly or wrong with pictures', 'type' => 'interactive', 'instructions' => 'Show silly pictures; ask what is wrong.', 'materials' => 'Images with errors', 'procedure' => 'Record identified anomalies.'],
                    ['text' => 'Matches upper case letters and lower case letters', 'type' => 'interactive', 'instructions' => 'Present letter tiles; ask to match uppercase to lowercase.', 'materials' => 'Letter tiles or on-screen', 'procedure' => 'Record correct matches.'],
                ],
            ],
            [
                'name' => 'Social-Emotional',
                'description' => 'Social-emotional skills (24 items)',
                'items' => [
                    ['text' => 'Enjoys watching activities of nearby people or animals', 'type' => 'static', 'instructions' => 'Observe interest in surroundings.', 'materials' => 'None', 'procedure' => 'Note attention and enjoyment.'],
                    ['text' => 'Friendly with strangers but initially may show slight anxiety or shyness', 'type' => 'static', 'instructions' => 'Observe reactions to unfamiliar adults.', 'materials' => 'None', 'procedure' => 'Note approach and hesitation.'],
                    ['text' => 'Plays alone but likes to be near familiar adults or siblings', 'type' => 'static', 'instructions' => 'Observe proximity preference during solo play.', 'materials' => 'None', 'procedure' => 'Note location and comfort.'],
                    ['text' => 'Laughs or squeals aloud in play', 'type' => 'static', 'instructions' => 'Observe vocal expressions during play.', 'materials' => 'None', 'procedure' => 'Record frequency of laughter.'],
                    ['text' => 'Plays peek-a-boo', 'type' => 'static', 'instructions' => 'Engage in peek-a-boo.', 'materials' => 'Cloth/hands', 'procedure' => 'Observe participation and timing.'],
                    ['text' => 'Rolls ball interactively with caregiver/examiner', 'type' => 'static', 'instructions' => 'Sit opposite child and roll ball.', 'materials' => 'Soft ball', 'procedure' => 'Observe turn-taking and reciprocity.'],
                    ['text' => 'Hugs or cuddles toys', 'type' => 'static', 'instructions' => 'Observe affection toward toys.', 'materials' => 'Stuffed toy', 'procedure' => 'Parental report or observation.'],
                    ['text' => 'Demonstrates respect for elders using terms like "po" and "opo"', 'type' => 'static', 'instructions' => 'Observe greeting language in context.', 'materials' => 'None', 'procedure' => 'Note culturally appropriate terms.'],
                    ['text' => 'Shares toys with others', 'type' => 'static', 'instructions' => 'Observe sharing during play.', 'materials' => 'Toys', 'procedure' => 'Parental report and observation.'],
                    ['text' => 'Imitates adult activities (e.g., cooking, washing)', 'type' => 'static', 'instructions' => 'Observe pretend adult actions.', 'materials' => 'Play props', 'procedure' => 'Record types of imitated activities.'],
                    ['text' => 'Identifies feelings in others', 'type' => 'static', 'instructions' => 'Discuss feelings; observe recognition.', 'materials' => 'None', 'procedure' => 'Ask child how someone might feel; record accuracy.'],
                    ['text' => 'Appropriately uses cultural gestures of greeting', 'type' => 'static', 'instructions' => 'Observe greetings (mano, bless, kiss).', 'materials' => 'None', 'procedure' => 'Record gesture use with minimal prompting.'],
                    ['text' => 'Comforts playmates/siblings in distress', 'type' => 'static', 'instructions' => 'Observe responses to others upset.', 'materials' => 'None', 'procedure' => 'Record comforting behaviors.'],
                    ['text' => 'Persists when faced with a problem or obstacle', 'type' => 'static', 'instructions' => 'Provide mild challenge; observe persistence.', 'materials' => 'Simple task with difficulty', 'procedure' => 'Record attempts before seeking help.'],
                    ['text' => 'Helps with family chores (wiping tables, watering plants)', 'type' => 'static', 'instructions' => 'Observe participation in simple chores.', 'materials' => 'Household items', 'procedure' => 'Record independence and consistency.'],
                    ['text' => 'Curious about environment but knows when to stop asking questions', 'type' => 'static', 'instructions' => 'Observe questions and response to limits.', 'materials' => 'None', 'procedure' => 'Record curiosity and restraint.'],
                    ['text' => 'Waits for his turn', 'type' => 'static', 'instructions' => 'Observe turn-taking in games.', 'materials' => 'Simple game', 'procedure' => 'Record waiting without prompting.'],
                    ['text' => 'Asks permission to play with toy being used by another', 'type' => 'static', 'instructions' => 'Observe requesting behavior.', 'materials' => 'Toys', 'procedure' => 'Record asking before taking.'],
                    ['text' => 'Defends possessions with determination', 'type' => 'static', 'instructions' => 'Observe response when toy is taken.', 'materials' => 'Toys', 'procedure' => 'Record assertion and negotiation.'],
                    ['text' => 'Plays organized group games fairly', 'type' => 'static', 'instructions' => 'Observe group play and rules.', 'materials' => 'Group game', 'procedure' => 'Record fairness and lack of cheating.'],
                    ['text' => 'Can talk about complex feelings he experiences', 'type' => 'static', 'instructions' => 'Discuss feelings; prompt reflection.', 'materials' => 'None', 'procedure' => 'Record articulation of complex emotions.'],
                    ['text' => 'Honors a simple bargain with caregiver', 'type' => 'static', 'instructions' => 'Make simple agreement; observe follow-through.', 'materials' => 'None', 'procedure' => 'Record compliance with bargain.'],
                    ['text' => 'Watches responsibly over younger siblings/family members', 'type' => 'static', 'instructions' => 'Parental report on responsibility.', 'materials' => 'None', 'procedure' => 'Record supervision behaviors.'],
                    ['text' => 'Cooperates with adults and peers in group situations to minimize conflicts', 'type' => 'static', 'instructions' => 'Observe cooperation during group tasks.', 'materials' => 'Group activity', 'procedure' => 'Record collaborative behaviors.'],
                ],
            ],
        ];

        foreach ($domains as $domain) {
            $domainId = DB::table('domains')->where('name', $domain['name'])->value('id');
            if (!$domainId) {
                $domainId = DB::table('domains')->insertGetId([
                    'name' => $domain['name'],
                    'description' => $domain['description'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($domain['items'] as $item) {
                $exists = DB::table('questions')
                    ->where('domain_id', $domainId)
                    ->where('question_text', $item['text'])
                    ->exists();
                if ($exists) {
                    continue;
                }
                DB::table('questions')->insert([
                    'domain_id' => $domainId,
                    'question_text' => $item['text'],
                    'type' => $item['type'],
                    'instructions' => $item['instructions'] ?? null,
                    'materials' => $item['materials'] ?? null,
                    'procedure' => $item['procedure'] ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
