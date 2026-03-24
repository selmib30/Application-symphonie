<?php

namespace App\DataFixtures;

use App\Entity\Race;
use App\Entity\Skill;
use App\Entity\CharacterClass;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 1. Création des Compétences (Skills)
        $skillsData = [
            ['Acrobaties', 'DEX'], ['Arcanes', 'INT'], ['Athlétisme', 'STR'],
            ['Discrétion', 'DEX'], ['Dressage', 'WIS'], ['Escamotage', 'DEX'],
            ['Histoire', 'INT'], ['Intimidation', 'CHA'], ['Investigation', 'INT'],
            ['Médecine', 'WIS'], ['Nature', 'INT'], ['Perception', 'WIS'],
            ['Perspicacité', 'WIS'], ['Persuasion', 'CHA'], ['Religion', 'INT'],
            ['Représentation', 'CHA'], ['Survie', 'WIS'], ['Tromperie', 'CHA'],
        ];

        $skillsEntities = [];
        foreach ($skillsData as [$name, $ability]) {
            $skill = new Skill();
            $skill->setName($name);
            $skill->setAbility($ability);
            $manager->persist($skill);
            $skillsEntities[$name] = $skill;
        }

        // 2. Création des Races
        $racesData = [
            ['Humain', 'Polyvalents et ambitieux, les humains sont la race la plus répandue.'],
            ['Elfe', 'Gracieux et longévifs, les elfes possèdent une affinité naturelle avec la magie.'],
            ['Nain', 'Robustes et tenaces, les nains sont des artisans et guerriers réputés.'],
            ['Halfelin', 'Petits et agiles, les halfelins sont connus pour leur chance et leur discrétion.'],
            ['Demi-Orc', 'Forts et endurants, les demi-orcs allient la puissance des orcs à l\'adaptabilité humaine.'],
            ['Gnome', 'Curieux et inventifs, les gnomes excellent dans les domaines de la magie et de la technologie.'],
            ['Tieffelin', 'Descendants d\'une lignée infernale, les tieffelins portent la marque de leur héritage.'],
            ['Demi-Elfe', 'Héritant du meilleur des deux mondes, les demi-elfes sont diplomates et polyvalents.'],
        ];

        foreach ($racesData as [$name, $desc]) {
            $race = new Race();
            $race->setName($name);
            $race->setDescription($desc);
            $manager->persist($race);
        }

        // 3. Création des Classes (avec associations aux Skills)
        $classesData = [
            ['Barbare', 12, 'Guerrier sauvage animé par une rage dévastatrice.', ['Athlétisme', 'Intimidation']],
            ['Barde', 8, 'Artiste et conteur dont la musique possède un pouvoir magique.', ['Représentation', 'Tromperie', 'Persuasion']],
            ['Clerc', 8, 'Serviteur divin canalisant la puissance de sa divinité.', ['Religion', 'Médecine', 'Perspicacité']],
            ['Druide', 8, 'Gardien de la nature capable de se métamorphoser.', ['Nature', 'Dressage', 'Survie']],
            ['Guerrier', 10, 'Maître des armes et des tactiques de combat.', ['Athlétisme', 'Histoire']],
            ['Mage', 6, 'Érudit de l\'arcane maîtrisant de puissants sortilèges.', ['Arcanes', 'Investigation', 'Histoire']],
            ['Paladin', 10, 'Chevalier sacré combinant prouesse martiale et magie divine.', ['Religion', 'Persuasion', 'Intimidation']],
            ['Ranger', 10, 'Chasseur et pisteur expert des terres sauvages.', ['Survie', 'Perception', 'Nature']],
            ['Sorcier', 6, 'Lanceur de sorts dont le pouvoir est inné et instinctif.', ['Arcanes', 'Tromperie']],
            ['Voleur', 8, 'Spécialiste de la discrétion, du crochetage et des attaques sournoises.', ['Discrétion', 'Escamotage', 'Acrobaties']],
        ];

        foreach ($classesData as [$name, $hp, $desc, $associatedSkills]) {
            $class = new CharacterClass();
            $class->setName($name);
            $class->setHealthDice($hp);
            $class->setDescription($desc);

            // Association des compétences
            foreach ($associatedSkills as $skillName) {
                if (isset($skillsEntities[$skillName])) {
                    $class->addSkill($skillsEntities[$skillName]);
                }
            }

            $manager->persist($class);
        }

        $manager->flush();
    }
}
