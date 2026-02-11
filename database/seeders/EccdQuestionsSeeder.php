<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EccdQuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $domains = [
            [
                'name' => 'Gross Motor',
                'description' => 'Gross motor development (13 items)',
                'items' => [
                    ['order' => 1,  'text' => 'Climbs on chair or other elevated furniture without help', 'display_text' => 'Can your child climb onto a low chair or bed by themselves?', 'type' => 'static', 'instructions' => 'Observe child attempting to climb safely.', 'materials' => 'Chair or low bed', 'procedure' => 'Invite child to climb up and down with supervision.'],
                    ['order' => 2,  'text' => 'Walks backwards', 'display_text' => 'Can your child walk a few steps backwards?', 'type' => 'static', 'instructions' => 'Ask child to walk backwards a few steps.', 'materials' => 'Open space', 'procedure' => 'Demonstrate, then observe independent walking backwards.'],
                    ['order' => 3,  'text' => 'Runs without tripping or falling', 'display_text' => 'Can your child run without easily tripping or falling?', 'type' => 'static', 'instructions' => 'Ask child to run a short distance.', 'materials' => 'Open space', 'procedure' => 'Observe natural running pace over clear area.'],
                    ['order' => 4,  'text' => 'Walks down stairs, two feet on each step, with one hand held', 'display_text' => 'Can your child go down the stairs holding your hand, using two feet on each step?', 'type' => 'static', 'instructions' => 'Assist child down stairs with hand held.', 'materials' => 'Stairs with railing', 'procedure' => 'Provide support and observe stepping pattern.'],
                    ['order' => 5,  'text' => 'Walks upstairs holding onto a handrail, two feet on each step', 'display_text' => 'Can your child go up the stairs while holding the rail, using two feet on each step?', 'type' => 'static', 'instructions' => 'Ask child to ascend with handrail.', 'materials' => 'Stairs with railing', 'procedure' => 'Observe stepping pattern (two feet per step).'],
                    ['order' => 6,  'text' => 'Walks upstairs with alternate feet without holding onto a handrail', 'display_text' => 'Can your child go up the stairs without holding the rail, using one foot per step?', 'type' => 'static', 'instructions' => 'Ask child to ascend without handrail.', 'materials' => 'Stairs', 'procedure' => 'Observe alternating feet each step.'],
                    ['order' => 7,  'text' => 'Walks downstairs with alternate feet without holding onto a handrail', 'display_text' => 'Can your child go down the stairs without holding the rail, using one foot per step?', 'type' => 'static', 'instructions' => 'Ask child to descend without handrail.', 'materials' => 'Stairs', 'procedure' => 'Observe alternating feet each step.'],
                    ['order' => 8,  'text' => 'Moves body part as directed', 'display_text' => 'When you ask, does your child move body parts (like arms or legs) as told?', 'type' => 'static', 'instructions' => 'Give simple movement commands.', 'materials' => 'None', 'procedure' => 'Ask child to move arms, legs, head on cue.'],
                    ['order' => 9,  'text' => 'Jumps up', 'display_text' => 'Can your child jump up with both feet from the floor?', 'type' => 'static', 'instructions' => 'Ask child to jump in place.', 'materials' => 'Open space', 'procedure' => 'Observe vertical jump; note balance and take-off.'],
                    ['order' => 10, 'text' => 'Throws ball overhead with direction', 'display_text' => 'Can your child throw a ball overhand toward a person or target?', 'type' => 'static', 'instructions' => 'Ask child to throw ball overhead toward target.', 'materials' => 'Soft ball; target', 'procedure' => 'Observe overhand throw aiming toward target.'],
                    ['order' => 11, 'text' => 'Hops one to three steps on preferred foot', 'display_text' => 'Can your child hop forward on one foot for one to three hops?', 'type' => 'static', 'instructions' => 'Ask child to hop on one foot.', 'materials' => 'Open space', 'procedure' => 'Observe up to three consecutive hops on same foot.'],
                    ['order' => 12, 'text' => 'Jumps and turns', 'display_text' => 'Can your child jump and turn their body in the air?', 'type' => 'static', 'instructions' => 'Ask child to jump and rotate body.', 'materials' => 'Open space', 'procedure' => 'Observe jump with turn (any direction).'],
                    ['order' => 13, 'text' => 'Dances patterns/joins group movement activities', 'display_text' => 'Can your child follow simple dance moves or group actions?', 'type' => 'static', 'instructions' => 'Invite child to follow simple dance pattern.', 'materials' => 'Music (optional)', 'procedure' => 'Lead a short routine; observe participation and coordination.'],
                ],
            ],
            [
                'name' => 'Fine Motor',
                'description' => 'Fine motor development (11 items)',
                'items' => [
                    ['order' => 1,  'text' => 'Uses all five fingers to get food/toys on a flat surface', 'display_text' => 'Does your child use all fingers to pick up small toys or food from a table?', 'type' => 'static', 'instructions' => 'Place small items on table.', 'materials' => 'Small toys or snacks', 'procedure' => 'Observe grasp using all fingers to retrieve items.'],
                    ['order' => 2,  'text' => 'Picks up objects with thumb and index finger', 'display_text' => 'Can your child pick up a small item using just thumb and index finger (like a pinch)?', 'type' => 'static', 'instructions' => 'Present small bead or raisin.', 'materials' => 'Beads/raisins', 'procedure' => 'Observe pincer grasp.'],
                    ['order' => 3,  'text' => 'Displays a definite hand preference', 'display_text' => 'Does your child mostly use the same hand when reaching for things?', 'type' => 'static', 'instructions' => 'Offer tasks that require reaching/grasping.', 'materials' => 'Common objects', 'procedure' => 'Note consistent use of left/right hand across tasks.'],
                    ['order' => 4,  'text' => 'Puts small objects in/out of containers', 'display_text' => 'Can your child put small things into a cup and take them out again?', 'type' => 'static', 'instructions' => 'Provide container and small items.', 'materials' => 'Cup/box and small objects', 'procedure' => 'Observe placing and removing items.'],
                    ['order' => 5,  'text' => 'Holds crayon with all fingers making a fist (palmar grasp)', 'display_text' => 'When holding a crayon, does your child grab it with the whole hand like a fist?', 'type' => 'static', 'instructions' => 'Give crayon to child.', 'materials' => 'Crayon and paper', 'procedure' => 'Observe grasp style and scribbling.'],
                    ['order' => 6,  'text' => 'Unscrews lid of a container or unwraps food', 'display_text' => 'Can your child open a jar lid or unwrap a small snack?', 'type' => 'static', 'instructions' => 'Provide jar with loose lid.', 'materials' => 'Jar or wrapped snack', 'procedure' => 'Observe unscrewing or unwrapping.'],
                    ['order' => 7,  'text' => 'Scribbles spontaneously', 'display_text' => 'Does your child scribble on paper without being shown what to draw?', 'type' => 'static', 'instructions' => 'Offer paper and crayon.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe spontaneous scribbling.'],
                    ['order' => 8,  'text' => 'Scribbles vertical and horizontal lines', 'display_text' => 'Can your child draw up-and-down and side-to-side lines when asked?', 'type' => 'static', 'instructions' => 'Prompt child to draw lines.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe vertical and horizontal strokes.'],
                    ['order' => 9,  'text' => 'Draws circle purposefully', 'display_text' => 'Can your child draw a simple round circle when asked?', 'type' => 'static', 'instructions' => 'Ask child to draw a circle.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe circular drawing.'],
                    ['order' => 10, 'text' => 'Draws a human figure (head, eyes, trunk, arms, hands/fingers)', 'display_text' => 'Can your child draw a person with head, body, arms, and hands or fingers?', 'type' => 'static', 'instructions' => 'Prompt child to draw a person.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe included parts in drawing.'],
                    ['order' => 11, 'text' => 'Draws a house using geometric forms', 'display_text' => 'Can your child draw a simple house using shapes (like a square with a triangle roof)?', 'type' => 'static', 'instructions' => 'Ask child to draw a house.', 'materials' => 'Paper, crayons', 'procedure' => 'Observe use of shapes (square, triangle).'],
                ],
            ],
            [
                'name' => 'Self-Help',
                'description' => 'Self-help skills (27 items)',
                'items' => [
                    ['order' => 1,  'text' => 'Feeds self with finger food using fingers', 'display_text' => 'Does your child feed themselves finger foods (like biscuits or bread) using their own fingers?', 'type' => 'static', 'instructions' => 'Offer finger foods.', 'materials' => 'Biscuits or bread', 'procedure' => 'Observe self-feeding and grasp.'],
                    ['order' => 2,  'text' => 'Feeds self using fingers to eat rice/viands with spillage', 'display_text' => 'Does your child eat rice and viands with their fingers, even if some food spills?', 'type' => 'static', 'instructions' => 'Provide small meal sample.', 'materials' => 'Rice/viands sample', 'procedure' => 'Observe self-feeding and spillage.'],
                    ['order' => 3,  'text' => 'Feeds self using spoon with spillage', 'display_text' => 'Does your child feed themselves with a spoon, even if some food spills?', 'type' => 'static', 'instructions' => 'Provide spoon and bowl.', 'materials' => 'Spoon, bowl with food', 'procedure' => 'Observe scooping and bringing to mouth.'],
                    ['order' => 4,  'text' => 'Feeds self using fingers without spillage', 'display_text' => 'Can your child eat with their fingers with little or no spilling?', 'type' => 'static', 'instructions' => 'Offer finger foods.', 'materials' => 'Finger foods', 'procedure' => 'Observe minimal spillage.'],
                    ['order' => 5,  'text' => 'Feeds self using spoon without spillage', 'display_text' => 'Can your child feed themselves with a spoon without spilling much?', 'type' => 'static', 'instructions' => 'Provide spoon and bowl.', 'materials' => 'Spoon, bowl with food', 'procedure' => 'Observe control and no spillage.'],
                    ['order' => 6,  'text' => 'Eats without need for spoonfeeding during any meal', 'display_text' => 'During meals, does your child eat on their own without needing to be spoon-fed?', 'type' => 'static', 'instructions' => 'Observe typical meal behavior.', 'materials' => 'Usual meal', 'procedure' => 'Note independence throughout meal.'],
                    ['order' => 7,  'text' => 'Helps hold cup for drinking', 'display_text' => 'When drinking from a cup, does your child help hold the cup?', 'type' => 'static', 'instructions' => 'Provide cup with water.', 'materials' => 'Cup with water', 'procedure' => 'Observe assisting in holding cup.'],
                    ['order' => 8,  'text' => 'Drinks from cup with spillage', 'display_text' => 'Can your child drink from a cup, even if some water spills?', 'type' => 'static', 'instructions' => 'Provide cup with water.', 'materials' => 'Cup with water', 'procedure' => 'Observe drinking and spillage.'],
                    ['order' => 9,  'text' => 'Drinks from cup unassisted', 'display_text' => 'Can your child drink from a cup alone without help?', 'type' => 'static', 'instructions' => 'Provide cup with water.', 'materials' => 'Cup with water', 'procedure' => 'Observe unassisted drinking.'],
                    ['order' => 10, 'text' => 'Gets drink for self unassisted', 'display_text' => 'Can your child get their own drink (like water) without help?', 'type' => 'static', 'instructions' => 'Ask child to get a drink.', 'materials' => 'Accessible water source', 'procedure' => 'Observe initiation and completion.'],
                    ['order' => 11, 'text' => 'Pours from pitcher without spillage', 'display_text' => 'Can your child pour water from a small pitcher into a cup without spilling much?', 'type' => 'static', 'instructions' => 'Provide small pitcher and cup.', 'materials' => 'Pitcher and cup', 'procedure' => 'Observe pouring control.'],
                    ['order' => 12, 'text' => 'Prepares own food/snack', 'display_text' => 'Can your child prepare a simple snack by themselves (like spreading something on bread)?', 'type' => 'static', 'instructions' => 'Invite child to make a simple snack.', 'materials' => 'Bread, spread, utensils', 'procedure' => 'Observe steps and safety.'],
                    ['order' => 13, 'text' => 'Prepares meals for younger siblings when no adult is around', 'display_text' => 'Can your child prepare a very simple meal for a younger sibling when no adult is nearby?', 'type' => 'static', 'instructions' => 'Discuss/observe capability for simple meal prep.', 'materials' => 'Simple meal items', 'procedure' => 'Parental report or supervised demo.'],
                    ['order' => 14, 'text' => 'Participates when being dressed (raises arms or lifts leg)', 'display_text' => 'When getting dressed, does your child help by raising arms or lifting legs?', 'type' => 'static', 'instructions' => 'Prompt help during dressing.', 'materials' => 'Clothing', 'procedure' => 'Observe cooperation with prompts.'],
                    ['order' => 15, 'text' => 'Pulls down gartered short pants', 'display_text' => 'Can your child pull down elastic shorts on their own?', 'type' => 'static', 'instructions' => 'Ask child to pull down shorts.', 'materials' => 'Shorts', 'procedure' => 'Observe ability and coordination.'],
                    ['order' => 16, 'text' => 'Removes sando', 'display_text' => 'Can your child take off a sleeveless shirt (sando) by themselves?', 'type' => 'static', 'instructions' => 'Ask child to remove undershirt.', 'materials' => 'Sando/undershirt', 'procedure' => 'Observe steps and independence.'],
                    ['order' => 17, 'text' => 'Dresses without assistance except for buttons and tying', 'display_text' => 'Can your child put on clothes by themselves, except for buttons and tying laces?', 'type' => 'static', 'instructions' => 'Provide clothing with simple fasteners.', 'materials' => 'Garments', 'procedure' => 'Observe dressing mostly independently.'],
                    ['order' => 18, 'text' => 'Dresses without assistance including buttons and tying', 'display_text' => 'Can your child fully dress themselves, including buttons and tying shoes?', 'type' => 'static', 'instructions' => 'Provide clothing with buttons and laces.', 'materials' => 'Buttoned shirt, shoes with laces', 'procedure' => 'Observe full independence.'],
                    ['order' => 19, 'text' => 'Informs adult only after urinating/defecating in underpants', 'display_text' => 'Does your child tell an adult only after they have already wet or soiled their underwear?', 'type' => 'static', 'instructions' => 'Parental report about toileting cues.', 'materials' => 'None', 'procedure' => 'Discuss recent events and patterns.'],
                    ['order' => 20, 'text' => 'Informs adult of need to urinate/defecate to be brought to designated place', 'display_text' => 'Does your child tell you when they need to pee or poop so you can bring them to the toilet?', 'type' => 'static', 'instructions' => 'Parental report about toileting cues.', 'materials' => 'None', 'procedure' => 'Discuss anticipation of toileting.'],
                    ['order' => 21, 'text' => 'Goes to designated place to urinate/defecate but sometimes still uses underpants', 'display_text' => 'Does your child usually go to the toilet but still sometimes uses their underwear?', 'type' => 'static', 'instructions' => 'Parental report and observation.', 'materials' => 'Bathroom', 'procedure' => 'Note consistency and accidents.'],
                    ['order' => 22, 'text' => 'Goes to designated place to urinate/defecate and never uses underpants anymore', 'display_text' => 'Does your child always use the toilet now and no longer uses their underwear?', 'type' => 'static', 'instructions' => 'Parental report and observation.', 'materials' => 'Bathroom', 'procedure' => 'Confirm complete toilet training.'],
                    ['order' => 23, 'text' => 'Wipes/cleans self after bowel movement', 'display_text' => 'After pooping, can your child wipe or clean themselves?', 'type' => 'static', 'instructions' => 'Discuss and observe if appropriate.', 'materials' => 'Bathroom supplies', 'procedure' => 'Confirm ability and hygiene.'],
                    ['order' => 24, 'text' => 'Participates when bathing (e.g., rubbing arms with soap)', 'display_text' => 'When bathing, does your child help wash their body (like rubbing arms with soap)?', 'type' => 'static', 'instructions' => 'Observe child during bathing.', 'materials' => 'Soap and water', 'procedure' => 'Note engagement and technique.'],
                    ['order' => 25, 'text' => 'Washes and dries hands without any help', 'display_text' => 'Can your child wash and dry their hands on their own?', 'type' => 'static', 'instructions' => 'Ask child to wash hands.', 'materials' => 'Sink, soap, towel', 'procedure' => 'Observe full sequence independently.'],
                    ['order' => 26, 'text' => 'Washes face without any help', 'display_text' => 'Can your child wash their face by themselves?', 'type' => 'static', 'instructions' => 'Ask child to wash face.', 'materials' => 'Sink, soap, towel', 'procedure' => 'Observe independence and thoroughness.'],
                    ['order' => 27, 'text' => 'Bathes without any help', 'display_text' => 'Can your child take a bath on their own, with an adult just watching for safety?', 'type' => 'static', 'instructions' => 'Parental report; supervise if needed.', 'materials' => 'Bathing materials', 'procedure' => 'Confirm ability to bathe independently.'],
                ],
            ],
            [
                'name' => 'Receptive Language',
                'description' => 'Understanding language (5 items)',
                'items' => [
                    ['order' => 1, 'text' => 'Points to a family member when asked', 'display_text' => 'When you name a family member, does your child point to that person or picture?', 'type' => 'static', 'instructions' => 'Ask child to point to named family member.', 'materials' => 'Presence of family or photos', 'procedure' => 'Observe accurate pointing.'],
                    ['order' => 2, 'text' => 'Points to five body parts on himself when asked', 'display_text' => 'When you ask, can your child point to different body parts on themselves (like nose, eyes, hands)?', 'type' => 'static', 'instructions' => 'Ask child to point to named body parts.', 'materials' => 'None', 'procedure' => 'Observe accurate pointing on self.'],
                    ['order' => 3, 'text' => 'Follows one-step instructions with simple prepositions', 'display_text' => 'Can your child follow a one-step instruction with words like in, on, or under ("Put the toy on the table")?', 'type' => 'static', 'instructions' => 'Give one-step commands (in, on, under).', 'materials' => 'Physical objects', 'procedure' => 'Observe compliance with prepositions.'],
                    ['order' => 4, 'text' => 'Follows two-step instructions with simple prepositions', 'display_text' => 'Can your child follow a two-step instruction with in, on, or under ("Pick up the toy and put it in the box")?', 'type' => 'static', 'instructions' => 'Give two-step commands.', 'materials' => 'Physical objects', 'procedure' => 'Observe sequencing and prepositions.'],
                    ['order' => 5, 'text' => 'Points to five named pictured objects when asked to do so', 'display_text' => 'When shown pictures, can your child point to the right picture when you name it?', 'type' => 'interactive', 'instructions' => 'Show picture board and ask child to point/click.', 'materials' => 'Picture cards or on-screen images', 'procedure' => 'Present images and record correct selections.'],
                ],
            ],
            [
                'name' => 'Expressive Language',
                'description' => 'Expressing language (8 items)',
                'items' => [
                    ['order' => 1, 'text' => 'Uses five to 20 recognizable words', 'display_text' => 'Does your child use about five to twenty clear words (like mama, water, ball)?', 'type' => 'static', 'instructions' => 'Parental report; sample vocabulary.', 'materials' => 'None', 'procedure' => 'Ask caregiver to list words; validate in play.'],
                    ['order' => 2, 'text' => 'Uses pronouns (I, me, ako, akin)', 'display_text' => 'Does your child use pronouns like "I", "me", or Filipino words like "ako", "akin"?', 'type' => 'static', 'instructions' => 'Parental report; observe in conversation.', 'materials' => 'None', 'procedure' => 'Prompt dialogue and note pronoun use.'],
                    ['order' => 3, 'text' => 'Uses two- to three-word verb-noun combinations', 'display_text' => 'Does your child say short phrases like "want milk" or "play ball"?', 'type' => 'static', 'instructions' => 'Parental report; observe phrases.', 'materials' => 'None', 'procedure' => 'Elicit requests and note structure.'],
                    ['order' => 4, 'text' => 'Speaks in grammatically correct two- to three-word sentences', 'display_text' => 'Does your child use short sentences that are mostly correct (like "I want water")?', 'type' => 'static', 'instructions' => 'Parental report; observe sentences.', 'materials' => 'None', 'procedure' => 'Prompt storytelling and note grammar.'],
                    ['order' => 5, 'text' => 'Asks "what" questions', 'display_text' => 'Does your child ask questions starting with "what" (like "What is that?")?', 'type' => 'static', 'instructions' => 'Parental report; observe questioning.', 'materials' => 'None', 'procedure' => 'Note spontaneous question forms.'],
                    ['order' => 6, 'text' => 'Asks "who" and "why" questions', 'display_text' => 'Does your child ask "who" or "why" questions (like "Who is that?", "Why is it raining?")?', 'type' => 'static', 'instructions' => 'Parental report; observe questioning.', 'materials' => 'None', 'procedure' => 'Note use of who/why prompts.'],
                    ['order' => 7, 'text' => 'Gives account of recent experiences in order using past tense', 'display_text' => 'Can your child tell a simple story about something that already happened, in the right order?', 'type' => 'static', 'instructions' => 'Prompt recall of an event.', 'materials' => 'None', 'procedure' => 'Observe sequence and tense usage.'],
                    ['order' => 8, 'text' => 'Names objects in pictures', 'display_text' => 'When looking at pictures, can your child name what they see (like "dog", "ball")?', 'type' => 'interactive', 'instructions' => 'Show images and ask child to name.', 'materials' => 'Picture cards or on-screen images', 'procedure' => 'Record accuracy in naming.'],
                ],
            ],
            [
                'name' => 'Cognitive',
                'description' => 'Cognitive skills (21 items)',
                'items' => [
                    ['order' => 1,  'text' => 'Looks in the direction of fallen object', 'display_text' => 'When something falls, does your child look toward where it fell?', 'type' => 'static', 'instructions' => 'Drop object and observe gaze.', 'materials' => 'Small object', 'procedure' => 'Drop near child and watch tracking.'],
                    ['order' => 2,  'text' => 'Looks for a partially hidden object', 'display_text' => 'If you partly hide a toy, does your child look for it?', 'type' => 'static', 'instructions' => 'Hide object partially under cloth.', 'materials' => 'Object and cloth', 'procedure' => 'Observe search and retrieval.'],
                    ['order' => 3,  'text' => 'Imitates behavior just seen a few minutes earlier', 'display_text' => 'Can your child copy an action you did a few minutes ago (like clapping a pattern)?', 'type' => 'static', 'instructions' => 'Demonstrate simple action; observe later.', 'materials' => 'Simple props', 'procedure' => 'Assess delayed imitation.'],
                    ['order' => 4,  'text' => 'Offers an object but will not release it', 'display_text' => 'Does your child hold out a toy as if to give it, but not let go?', 'type' => 'static', 'instructions' => 'Request object; observe reluctance.', 'materials' => 'Toy', 'procedure' => 'Note offering gesture without release.'],
                    ['order' => 5,  'text' => 'Looks for a completely hidden object', 'display_text' => 'If you fully hide a toy under a cloth or box, does your child try to find it?', 'type' => 'static', 'instructions' => 'Hide object under cloth/box.', 'materials' => 'Object and cover', 'procedure' => 'Observe search behavior.'],
                    ['order' => 6,  'text' => 'Exhibits simple pretend play (feeds, puts doll to sleep)', 'display_text' => 'Does your child pretend to feed a doll or put it to sleep?', 'type' => 'static', 'instructions' => 'Provide doll and props.', 'materials' => 'Doll, toy dishes/blanket', 'procedure' => 'Observe pretend actions.'],
                    ['order' => 7,  'text' => 'Matches objects', 'display_text' => 'Can your child match objects that are the same (like two spoons or two blocks)?', 'type' => 'static', 'instructions' => 'Provide pairs of similar objects.', 'materials' => 'Spoons, balls, blocks', 'procedure' => 'Observe matching by attribute.'],
                    ['order' => 8,  'text' => 'Sorts based on shapes', 'display_text' => 'Can your child sort or put things together based on their shape?', 'type' => 'static', 'instructions' => 'Provide shape sorter.', 'materials' => 'Shape pieces', 'procedure' => 'Observe sorting by shapes.'],
                    ['order' => 9,  'text' => 'Sorts objects based on two attributes (size and color)', 'display_text' => 'Can your child sort objects using both color and size (like small red, big blue)?', 'type' => 'static', 'instructions' => 'Provide varied shapes by size and color.', 'materials' => 'Shapes of different colors/sizes', 'procedure' => 'Observe sorting using two attributes.'],
                    ['order' => 10, 'text' => 'Arranges objects according to size from smallest to biggest', 'display_text' => 'Can your child line up objects from smallest to biggest?', 'type' => 'static', 'instructions' => 'Provide graduated sizes.', 'materials' => 'Stacking cups or blocks', 'procedure' => 'Observe correct size ordering.'],
                    ['order' => 11, 'text' => 'Copies shapes', 'display_text' => 'Can your child copy simple shapes you draw (like a line or square)?', 'type' => 'static', 'instructions' => 'Show shapes; ask child to copy.', 'materials' => 'Paper, pencil', 'procedure' => 'Observe accuracy in copying.'],
                    ['order' => 12, 'text' => 'Can assemble simple puzzles', 'display_text' => 'Can your child put together a simple puzzle with just a few pieces?', 'type' => 'static', 'instructions' => 'Provide simple jigsaw/puzzle.', 'materials' => 'Simple puzzles', 'procedure' => 'Observe assembly strategy.'],
                    ['order' => 13, 'text' => 'Matches two to three colors', 'display_text' => 'Can your child match objects of the same color (two or three colors)?', 'type' => 'interactive', 'instructions' => 'Present color tiles on screen or cards.', 'materials' => 'Color tiles/cards or on-screen', 'procedure' => 'Ask child to match same colors.'],
                    ['order' => 14, 'text' => 'Matches pictures', 'display_text' => 'Can your child match two pictures that are the same?', 'type' => 'interactive', 'instructions' => 'Show picture pairs; ask to match.', 'materials' => 'Picture cards or on-screen', 'procedure' => 'Record correct matches.'],
                    ['order' => 15, 'text' => 'Names four to six colors', 'display_text' => 'Can your child name four to six different colors when shown?', 'type' => 'interactive', 'instructions' => 'Show colored swatches; ask child to name/select.', 'materials' => 'Color swatches or on-screen', 'procedure' => 'Record named or selected color labels.'],
                    ['order' => 16, 'text' => 'Names 3 animals or vegetables when asked', 'display_text' => 'Can your child name at least three animals or vegetables when asked or shown pictures?', 'type' => 'interactive', 'instructions' => 'Show images; ask child to name/select.', 'materials' => 'Animal/vegetable images', 'procedure' => 'Record correct items named or chosen.'],
                    ['order' => 17, 'text' => 'States what common household items are used for', 'display_text' => 'Can your child tell what common household things are used for (like a spoon or broom)?', 'type' => 'interactive', 'instructions' => 'Show item images; ask function.', 'materials' => 'Household item images', 'procedure' => 'Record correct function responses.'],
                    ['order' => 18, 'text' => 'Demonstrates understanding of opposites by completing a statement', 'display_text' => 'Can your child understand opposites, like choosing "hot" when you say "not cold"?', 'type' => 'interactive', 'instructions' => 'Present statements with images; ask child to select opposite.', 'materials' => 'Opposite pairs images or text', 'procedure' => 'Record correct completions.'],
                    ['order' => 19, 'text' => 'Points to left and right sides of body', 'display_text' => 'When asked left or right, can your child point to the correct side of their body?', 'type' => 'interactive', 'instructions' => 'Show body diagram; ask left/right.', 'materials' => 'Body diagram or on-screen', 'procedure' => 'Record correct side identification.'],
                    ['order' => 20, 'text' => 'Can state what is silly or wrong with pictures', 'display_text' => 'When shown a silly picture (like a fish in a tree), can your child say what is wrong?', 'type' => 'interactive', 'instructions' => 'Show silly pictures; ask what is wrong.', 'materials' => 'Images with errors', 'procedure' => 'Record identified anomalies.'],
                    ['order' => 21, 'text' => 'Matches upper case letters and lower case letters', 'display_text' => 'Can your child match big (capital) letters with their small (lowercase) versions?', 'type' => 'interactive', 'instructions' => 'Present letter tiles; ask to match uppercase to lowercase.', 'materials' => 'Letter tiles or on-screen', 'procedure' => 'Record correct matches.'],
                ],
            ],
            [
                'name' => 'Social-Emotional',
                'description' => 'Social-emotional skills (24 items)',
                'items' => [
                    ['order' => 1,  'text' => 'Enjoys watching activities of nearby people or animals', 'display_text' => 'Does your child enjoy watching people or animals nearby?', 'type' => 'static', 'instructions' => 'Observe interest in surroundings.', 'materials' => 'None', 'procedure' => 'Note attention and enjoyment.'],
                    ['order' => 2,  'text' => 'Friendly with strangers but initially may show slight anxiety or shyness', 'display_text' => 'Is your child generally friendly with new people, even if shy at first?', 'type' => 'static', 'instructions' => 'Observe reactions to unfamiliar adults.', 'materials' => 'None', 'procedure' => 'Note approach and hesitation.'],
                    ['order' => 3,  'text' => 'Plays alone but likes to be near familiar adults or siblings', 'display_text' => 'Does your child play alone but prefer to stay close to family members?', 'type' => 'static', 'instructions' => 'Observe proximity preference during solo play.', 'materials' => 'None', 'procedure' => 'Note location and comfort.'],
                    ['order' => 4,  'text' => 'Laughs or squeals aloud in play', 'display_text' => 'Does your child laugh or squeal out loud when playing?', 'type' => 'static', 'instructions' => 'Observe vocal expressions during play.', 'materials' => 'None', 'procedure' => 'Record frequency of laughter.'],
                    ['order' => 5,  'text' => 'Plays peek-a-boo', 'display_text' => 'Does your child enjoy and understand peek-a-boo games?', 'type' => 'static', 'instructions' => 'Engage in peek-a-boo.', 'materials' => 'Cloth/hands', 'procedure' => 'Observe participation and timing.'],
                    ['order' => 6,  'text' => 'Rolls ball interactively with caregiver/examiner', 'display_text' => 'Will your child roll a ball back and forth with you?', 'type' => 'static', 'instructions' => 'Sit opposite child and roll ball.', 'materials' => 'Soft ball', 'procedure' => 'Observe turn-taking and reciprocity.'],
                    ['order' => 7,  'text' => 'Hugs or cuddles toys', 'display_text' => 'Does your child hug or cuddle stuffed toys or dolls?', 'type' => 'static', 'instructions' => 'Observe affection toward toys.', 'materials' => 'Stuffed toy', 'procedure' => 'Parental report or observation.'],
                    ['order' => 8,  'text' => 'Demonstrates respect for elders using terms like "po" and "opo"', 'display_text' => 'Does your child use polite words for elders, like "po" and "opo"?', 'type' => 'static', 'instructions' => 'Observe greeting language in context.', 'materials' => 'None', 'procedure' => 'Note culturally appropriate terms.'],
                    ['order' => 9,  'text' => 'Shares toys with others', 'display_text' => 'Does your child share toys with other children at least sometimes?', 'type' => 'static', 'instructions' => 'Observe sharing during play.', 'materials' => 'Toys', 'procedure' => 'Parental report and observation.'],
                    ['order' => 10, 'text' => 'Imitates adult activities (e.g., cooking, washing)', 'display_text' => 'Does your child pretend to do adult activities, like cooking or washing?', 'type' => 'static', 'instructions' => 'Observe pretend adult actions.', 'materials' => 'Play props', 'procedure' => 'Record types of imitated activities.'],
                    ['order' => 11, 'text' => 'Identifies feelings in others', 'display_text' => 'Can your child tell how others might feel (like happy, sad, angry)?', 'type' => 'static', 'instructions' => 'Discuss feelings; observe recognition.', 'materials' => 'None', 'procedure' => 'Ask child how someone might feel; record accuracy.'],
                    ['order' => 12, 'text' => 'Appropriately uses cultural gestures of greeting', 'display_text' => 'Does your child use common greeting gestures in your culture (like mano or bless)?', 'type' => 'static', 'instructions' => 'Observe greetings (mano, bless, kiss).', 'materials' => 'None', 'procedure' => 'Record gesture use with minimal prompting.'],
                    ['order' => 13, 'text' => 'Comforts playmates/siblings in distress', 'display_text' => 'When another child is upset, does your child try to comfort them?', 'type' => 'static', 'instructions' => 'Observe responses to others upset.', 'materials' => 'None', 'procedure' => 'Record comforting behaviors.'],
                    ['order' => 14, 'text' => 'Persists when faced with a problem or obstacle', 'display_text' => 'When faced with a small challenge, does your child keep trying before giving up?', 'type' => 'static', 'instructions' => 'Provide mild challenge; observe persistence.', 'materials' => 'Simple task with difficulty', 'procedure' => 'Record attempts before seeking help.'],
                    ['order' => 15, 'text' => 'Helps with family chores (wiping tables, watering plants)', 'display_text' => 'Does your child help with simple chores, like wiping tables or watering plants?', 'type' => 'static', 'instructions' => 'Observe participation in simple chores.', 'materials' => 'Household items', 'procedure' => 'Record independence and consistency.'],
                    ['order' => 16, 'text' => 'Curious about environment but knows when to stop asking questions', 'display_text' => 'Is your child curious and asks questions, but can stop when told gently?', 'type' => 'static', 'instructions' => 'Observe questions and response to limits.', 'materials' => 'None', 'procedure' => 'Record curiosity and restraint.'],
                    ['order' => 17, 'text' => 'Waits for his turn', 'display_text' => 'Can your child wait for their turn during games or activities?', 'type' => 'static', 'instructions' => 'Observe turn-taking in games.', 'materials' => 'Simple game', 'procedure' => 'Record waiting without prompting.'],
                    ['order' => 18, 'text' => 'Asks permission to play with toy being used by another', 'display_text' => 'Does your child ask before taking a toy that another child is using?', 'type' => 'static', 'instructions' => 'Observe requesting behavior.', 'materials' => 'Toys', 'procedure' => 'Record asking before taking.'],
                    ['order' => 19, 'text' => 'Defends possessions with determination', 'display_text' => 'Does your child firmly stand up for their own things (without hurting others)?', 'type' => 'static', 'instructions' => 'Observe response when toy is taken.', 'materials' => 'Toys', 'procedure' => 'Record assertion and negotiation.'],
                    ['order' => 20, 'text' => 'Plays organized group games fairly', 'display_text' => 'In group games, does your child try to play fairly and follow rules?', 'type' => 'static', 'instructions' => 'Observe group play and rules.', 'materials' => 'Group game', 'procedure' => 'Record fairness and lack of cheating.'],
                    ['order' => 21, 'text' => 'Can talk about complex feelings he experiences', 'display_text' => 'Can your child talk about more complex feelings (like being nervous, jealous, or proud)?', 'type' => 'static', 'instructions' => 'Discuss feelings; prompt reflection.', 'materials' => 'None', 'procedure' => 'Record articulation of complex emotions.'],
                    ['order' => 22, 'text' => 'Honors a simple bargain with caregiver', 'display_text' => 'If you make a simple deal (like "Clean up then play"), does your child follow it?', 'type' => 'static', 'instructions' => 'Make simple agreement; observe follow-through.', 'materials' => 'None', 'procedure' => 'Record compliance with bargain.'],
                    ['order' => 23, 'text' => 'Watches responsibly over younger siblings/family members', 'display_text' => 'Can your child watch over a younger sibling in a responsible way for a short time?', 'type' => 'static', 'instructions' => 'Parental report on responsibility.', 'materials' => 'None', 'procedure' => 'Record supervision behaviors.'],
                    ['order' => 24, 'text' => 'Cooperates with adults and peers in group situations to minimize conflicts', 'display_text' => 'In group activities, does your child cooperate with others and help avoid fights or conflicts?', 'type' => 'static', 'instructions' => 'Observe cooperation during group tasks.', 'materials' => 'Group activity', 'procedure' => 'Record collaborative behaviors.'],
                ],
            ],
        ];

        // Insert or update domains and questions according to new schema
        foreach ($domains as $domain) {
            $domainId = DB::table('domains')
                ->where('name', $domain['name'])
                ->value('domain_id');

            if (!$domainId) {
                $domainId = DB::table('domains')->insertGetId([
                    'name' => $domain['name'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            foreach ($domain['items'] as $item) {
                $exists = DB::table('questions')
                    ->where('domain_id', $domainId)
                    ->where('text', $item['text'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('questions')->insert([
                    'domain_id' => $domainId,
                    'text' => $item['text'],
                    'question_type' => $item['type'],
                    'order' => $item['order'],
                    'display_text' => $item['display_text'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Adaptive Basal/Ceiling Testing (BEST LOGIC FOR FAMILY)
        // This is how the physical ECCD checklist was designed to be administered in person.
        // Each domain progresses from easier (lower order) to harder (higher order).
        // Basal — once the child gets N consecutive correct answers, assume everything below is also correct.
        // Ceiling — once the child gets N consecutive wrong answers, assume everything above is also wrong.
    }
}
