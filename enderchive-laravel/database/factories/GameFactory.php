<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Game>
 */
class GameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gameTitles = [
            'The Legend of Zelda: Breath of the Wild', 'Cyberpunk 2077', 'Elden Ring',
            'The Witcher 3: Wild Hunt', 'Red Dead Redemption 2', 'Grand Theft Auto V',
            'Minecraft', 'Fortnite', 'Among Us', 'Fall Guys', 'Valorant',
            'League of Legends', 'Dota 2', 'Counter-Strike 2', 'Apex Legends',
            'Call of Duty: Modern Warfare', 'FIFA 24', 'NBA 2K24', 'Rocket League',
            'Stardew Valley', 'Hollow Knight', 'Celeste', 'Cuphead', 'Ori and the Blind Forest',
            'God of War', 'Spider-Man', 'The Last of Us Part II', 'Ghost of Tsushima',
            'Horizon Zero Dawn', 'Uncharted 4: A Thief\'s End', 'Bloodborne', 'Dark Souls III',
            'Sekiro: Shadows Die Twice', 'Demon\'s Souls', 'Final Fantasy VII Remake',
            'Persona 5 Royal', 'Monster Hunter: World', 'Resident Evil Village',
            'Resident Evil 4 Remake', 'Dead Space Remake', 'Alan Wake II',
            'Baldur\'s Gate 3', 'Divinity: Original Sin 2', 'Pillars of Eternity',
            'Disco Elysium', 'Outer Wilds', 'Return of the Obra Dinn',
            'Hades', 'Dead Cells', 'Slay the Spire', 'Into the Breach',
            'FTL: Faster Than Light', 'Subnautica', 'No Man\'s Sky',
            'Terraria', 'Don\'t Starve Together', 'RimWorld', 'Factorio',
            'Cities: Skylines', 'Civilization VI', 'Total War: Warhammer III',
            'Crusader Kings III', 'Europa Universalis IV', 'Hearts of Iron IV',
            'World of Warcraft', 'Final Fantasy XIV', 'Guild Wars 2',
            'Destiny 2', 'Warframe', 'Path of Exile', 'Diablo IV',
            'Overwatch 2', 'Team Fortress 2', 'Left 4 Dead 2',
            'Portal 2', 'Half-Life: Alyx', 'Beat Saber', 'Superhot VR',
            'Job Simulator', 'Moss', 'Lone Echo', 'Asgard\'s Wrath',
            'Super Mario Odyssey', 'The Legend of Zelda: Tears of the Kingdom',
            'Animal Crossing: New Horizons', 'Pokémon Scarlet', 'Pokémon Violet',
            'Metroid Dread', 'Kirby and the Forgotten Land', 'Splatoon 3',
            'Mario Kart 8 Deluxe', 'Super Smash Bros. Ultimate', 'Fire Emblem: Three Houses',
            'Xenoblade Chronicles 3', 'Bayonetta 3', 'Astral Chain',
            'Octopath Traveler', 'Triangle Strategy', 'Live A Live',
            'Dragon Quest XI', 'Ni No Kuni II', 'Tales of Arise',
            'Scarlet Nexus', 'Code Vein', 'Nier: Automata', 'Nier Replicant',
            'Kingdom Hearts III', 'Final Fantasy XVI', 'Forspoken',
            'Marvel\'s Spider-Man: Miles Morales', 'Ratchet & Clank: Rift Apart',
            'Returnal', 'Deathloop', 'Kena: Bridge of Spirits',
            'Little Nightmares II', 'It Takes Two', 'A Way Out',
            'Hazelight Studios', 'Journey', 'Flower', 'Abzû',
            'Gris', 'Ori and the Will of the Wisps', 'Limbo', 'Inside',
            'Little Nightmares', 'Unravel', 'Unravel Two', 'Brothers: A Tale of Two Sons',
            'The Stanley Parable', 'The Beginner\'s Guide', 'Dr. Langeskov',
            'What Remains of Edith Finch', 'Gone Home', 'Firewatch',
            'Oxenfree', 'Night in the Woods', 'Life is Strange',
            'Life is Strange: Before the Storm', 'Life is Strange: True Colors',
            'The Walking Dead', 'The Wolf Among Us', 'Tales from the Borderlands',
            'Batman: The Telltale Series', 'Minecraft Dungeons', 'Diablo III',
            'Torchlight III', 'Grim Dawn', 'Titan Quest', 'Sacred 2',
            'Borderlands 3', 'Borderlands 2', 'Borderlands: The Pre-Sequel',
            'Tiny Tina\'s Wonderlands', 'Outriders', 'Remnant: From the Ashes',
            'Remnant II', 'The Division 2', 'Tom Clancy\'s Ghost Recon Wildlands',
            'Tom Clancy\'s Ghost Recon Breakpoint', 'Far Cry 6', 'Far Cry 5',
            'Assassin\'s Creed Valhalla', 'Assassin\'s Creed Odyssey',
            'Assassin\'s Creed Origins', 'Watch Dogs: Legion', 'Watch Dogs 2',
            'Tom Clancy\'s Rainbow Six Siege', 'Tom Clancy\'s Rainbow Six Extraction',
            'For Honor', 'Skull & Bones', 'Prince of Persia: The Sands of Time Remake',
            'Immortals Fenyx Rising', 'Just Dance 2024', 'Rocksmith+',
            'The Crew Motorfest', 'The Crew 2', 'Trackmania', 'Trials Rising',
            'Steep', 'Riders Republic', 'Skate 3', 'Session: Skate Sim',
            'Skate 4', 'Tony Hawk\'s Pro Skater 1+2', 'OlliOlli World',
            'Shredders', 'Descenders', 'Lonely Mountains: Downhill',
            'Art of Rally', 'WRC Generations', 'Dirt Rally 2.0',
            'F1 23', 'Gran Turismo 7', 'Forza Horizon 5', 'Forza Motorsport',
            'Project CARS 3', 'Assetto Corsa Competizione', 'iRacing',
            'Automobilista 2', 'rFactor 2', 'RaceRoom Racing Experience',
            'Need for Speed Unbound', 'Need for Speed Heat', 'Hot Wheels Unleashed',
            'Crash Team Racing Nitro-Fueled', 'Team Sonic Racing', 'Garfield Kart',
            'Nickelodeon Kart Racers 3', 'Mario Kart Live: Home Circuit',
            'Super Monkey Ball: Banana Mania', 'Fall Guys', 'Gang Beasts',
            'Human: Fall Flat', 'Totally Reliable Delivery Service',
            'Goat Simulator 3', 'Untitled Goose Game', 'Goat Simulator',
            'Surgeon Simulator 2', 'I Am Bread', 'Octodad: Dadliest Catch',
            'Katamari Damacy Reroll', 'We Love Katamari', 'Rolling Sky',
            'Super Monkey Ball', 'Marble It Up!', 'Marble Blast Ultra',
            'Switchball', 'Ballance', 'Hamsterball', 'Marble Madness',
            'Super Monkey Ball 2', 'Super Monkey Ball Deluxe',
            'Super Monkey Ball: Touch & Roll', 'Super Monkey Ball: Step & Roll',
            'Super Monkey Ball: Banana Blitz', 'Super Monkey Ball: Banana Splitz',
            'Super Monkey Ball: Banana Blitz HD', 'Super Monkey Ball: Banana Mania',
            'Super Monkey Ball: Banana Rumble', 'Super Monkey Ball: Banana Blitz HD',
            'Super Monkey Ball: Banana Blitz HD', 'Super Monkey Ball: Banana Blitz HD'
        ];

        $developers = [
            'Nintendo', 'CD Projekt Red', 'FromSoftware', 'Rockstar Games', 'Mojang Studios',
            'Epic Games', 'InnerSloth', 'Mediatonic', 'Riot Games', 'Valve Corporation',
            'Electronic Arts', 'Ubisoft', 'Activision', 'Sony Interactive Entertainment',
            'Microsoft Studios', 'ConcernedApe', 'Team Cherry', 'Matt Makes Games',
            'Studio MDHR', 'Moon Studios'
        ];

        $publishers = [
            'Nintendo', 'CD Projekt', 'Bandai Namco', 'Rockstar Games', 'Microsoft',
            'Epic Games', 'Riot Games', 'Valve', 'Electronic Arts', 'Ubisoft',
            'Activision', 'Sony', 'Devolver Digital', 'Team17', 'Annapurna Interactive'
        ];

        return [
            'title' => $this->faker->randomElement($gameTitles),
            'description' => $this->faker->paragraphs(3, true),
            'genre_id' => \App\Models\Genre::inRandomOrder()->first()->genre_id,
            'developer' => $this->faker->randomElement($developers),
            'publisher' => $this->faker->randomElement($publishers),
            'release_date' => $this->faker->dateTimeBetween('-10 years', 'now'),
            // Generate placeholder game cover image using Lorem Picsum
            // We'll use a random number to get different images for each game
            'cover_image' => 'https://picsum.photos/seed/' . $this->faker->numberBetween(1, 10000) . '/300/400',
        ];
    }
}
