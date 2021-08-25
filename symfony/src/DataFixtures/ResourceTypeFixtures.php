<?php

namespace App\DataFixtures;

use App\Entity\ResourceType;
use DateTime;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ResourceTypeFixtures extends Fixture implements FixtureGroupInterface {

    public function load(ObjectManager $manager){
        $resourceTypes = [
            ['name'=> 'Building', 'slug' => 'building'],
            ['name'=> 'Building Buffs', 'slug' => 'building_buffs'],
            ['name'=> 'Buildings', 'slug' => 'buildings'],
            ['name'=> 'Consumable', 'slug' => 'consumable'],
            ['name'=> 'Consumables', 'slug' => 'consumables'],
            ['name'=> 'Factions', 'slug' => 'factions'],
            ['name'=> 'Hostile', 'slug' => 'hostile'],
            ['name'=> 'Hostiles', 'slug' => 'hostiles'],
            ['name'=> 'Materials', 'slug' => 'materials'],
            ['name'=> 'Officer', 'slug' => 'officer'],
            ['name'=> 'Officer Division', 'slug' => 'officer_division'],
            ['name'=> 'Officers', 'slug' => 'officers'],
            ['name'=> 'Officers Synergy', 'slug' => 'officers_synergy'],
            ['name'=> 'Research', 'slug' => 'research'],
            ['name'=> 'Resource', 'slug' => 'resource'],
            ['name'=> 'Ship', 'slug' => 'ship'],
            ['name'=> 'Ship Component', 'slug' => 'ship_component'],
            ['name'=> 'Ship Type', 'slug' => 'ship_type'],
            ['name'=> 'Ships', 'slug' => 'ships'],
            ['name'=> 'System', 'slug' => 'system'],
            ['name'=> 'Systems', 'slug' => 'systems'],
        ];

        foreach ($resourceTypes as $resourceType) {
            $resource = new ResourceType();
            $resource
                ->setName($resourceType['name'])
                ->setSlug($resourceType['slug'])
                ->setCreated(new DateTime())
                ->setLastUpdate(new DateTime());
            $manager->persist($resource);
        }

        $manager->flush();
    }
    public static function getGroups(): array {
        return ['resource'];
    }
}