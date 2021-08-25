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
            ['name' => 'Building', 'slug' => 'building', 'url_type' => 1],
            ['name' => 'Building Buffs', 'slug' => 'building_buffs', 'url_type' => 2],
            ['name' => 'Buildings', 'slug' => 'buildings', 'url_type' => 2],
            ['name' => 'Consumable', 'slug' => 'consumable', 'url_type' => 1],
            ['name' => 'Consumables', 'slug' => 'consumables', 'url_type' => 2],
            ['name' => 'Factions', 'slug' => 'factions', 'url_type' => 2],
            ['name' => 'Hostile', 'slug' => 'hostile', 'url_type' => 1],
            ['name' => 'Hostiles', 'slug' => 'hostiles', 'url_type' => 2],
            ['name' => 'Materials', 'slug' => 'materials', 'url_type' => 2],
            ['name' => 'Officer', 'slug' => 'officer', 'url_type' => 1],
            ['name' => 'Officer Division', 'slug' => 'officer_division', 'url_type' => 2],
            ['name' => 'Officers', 'slug' => 'officers', 'url_type' => 2],
            ['name' => 'Officers Synergy', 'slug' => 'officers_synergy', 'url_type' => 2],
            ['name' => 'Research', 'slug' => 'research', 'url_type' => 1],
            ['name' => 'Resource', 'slug' => 'resource', 'url_type' => 1],
            ['name' => 'Ship', 'slug' => 'ship', 'url_type' => 1],
            ['name' => 'Ship Components', 'slug' => 'ship_components', 'url_type' => 2],
            ['name' => 'Ship Type', 'slug' => 'ship_type', 'url_type' => 2],
            ['name' => 'Ships', 'slug' => 'ships', 'url_type' => 2],
            ['name' => 'System', 'slug' => 'system', 'url_type' => 1],
            ['name' => 'Systems', 'slug' => 'systems', 'url_type' => 2],
        ];

        foreach ($resourceTypes as $resourceType) {
            $resource = new ResourceType();
            $resource
                ->setName($resourceType['name'])
                ->setSlug($resourceType['slug'])
                ->setUrlType($resourceType['url_type'])
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