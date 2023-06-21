<?php

namespace App\DataFixtures;

use App\Entity\Message;
use App\Entity\Picture;
use App\Entity\Tag;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    private $slugger;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@snowtricks.com')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->userPasswordHasher->hashPassword($admin, 'Admin1234*'))
            ->setUsername('Admin')
            ->setIsValid(true);
        $manager->persist($admin);

        $fixtures = [
            [
                'tag' => 'Grabs',
                'tricks' => [
                    [
                        'name' => 'Indy',
                        'description' => 'Saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ],
                    [
                        'name' => 'Japan air',
                        'description' => 'Saisie de l\'avant de la planche, avec la main avant, du côté de la carre frontside.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ]
                ]
            ],
            [
                'tag' => 'Rotations',
                'tricks' => [
                    [
                        'name' => '180',
                        'description' => 'Désigne un demi-tour.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ],
                    [
                        'name' => '360',
                        'description' => 'Désigne un tour complet.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ],
                    [
                        'name' => '720',
                        'description' => 'Désigne deux tours complets.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ],
                    [
                        'name' => '1080',
                        'description' => 'Désigne trois tours complets.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ]
                ]
            ],
            [
                'tag' => 'Flips',
                'tricks' => [
                    [
                        'name' => 'Front flip',
                        'description' => 'Désigne une rotation verticale vers l\'avant.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ],
                    [
                        'name' => 'Back flip',
                        'description' => 'Désigne une rotation verticale vers l\'arrière.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => [
                            'Incroyable backflip ! Le rider a réalisé une rotation arrière parfaite avec une grande maîtrise.',
                            'Le backflip est une figure classique du snowboard, et ce rider l\'a interprété avec puissance et élégance.',
                            'Le backflip est une figure audacieuse qui demande une parfaite coordination, et ce rider l\'a parfaitement exécuté.',
                            'Le snowboarder a enchaîné plusieurs backflips d\'affilée, démontrant une technique remarquable et une grande confiance en soi.',
                            'Le backflip est une figure emblématique du snowboard, et ce rider a ajouté sa propre touche de style à cette figure mythique.'
                        ]
                    ]
                ]
            ],
            [
                'tag' => 'Slides',
                'tricks' => [
                    [
                        'name' => 'Nose slide',
                        'description' => 'Désigne un slide avec l\'avant de la planche sur la barre.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ],
                    [
                        'name' => 'Tail slide',
                        'description' => 'Désigne un slide avec l\'arrière de la planche sur la barre.',
                        'picture' => 'default-trick-img.png',
                        'video' => [
                            'id' => 'kh29_SERH0Y',
                            'platform' => 'youtube'
                        ],
                        'message' => []
                    ]
                ]
            ]
        ];

        foreach ($fixtures as $dataFixtures) {
            // Ajout du tag
            $tag = new Tag();
            $tag->setName($dataFixtures['tag']);
            $manager->persist($tag);
            // Ajout du trick
            foreach ($dataFixtures['tricks'] as $dataTricks) {
                $trick = new Trick();
                $trick->setUser($admin)
                    ->setName($dataTricks['name'])
                    ->setSlug(strtolower($this->slugger->slug($trick->getName())))
                    ->setDescription($dataTricks['description'])
                    ->addTag($tag);
                $manager->persist($trick);

                $picture = new Picture();
                $picture->setName($dataTricks['picture'])
                    ->setTrick($trick);
                $manager->persist($picture);

                $video = new Video();
                $video->setTrick($trick)
                    ->setVideoId($dataTricks['video']['id'])
                    ->setPlatform($dataTricks['video']['platform']);
                $manager->persist($video);

                if (!empty($dataTricks['message'])) {
                    $u = 1;
                    foreach ($dataTricks['message'] as $dataMessage) {
                        $user = new User();
                        $user->setEmail("user$u@snowtricks.com")
                            ->setPassword($this->userPasswordHasher->hashPassword($user, 'User1234*'))
                            ->setUsername("User$u")
                            ->setIsValid(true);
                        $manager->persist($user);

                        $message = new Message();
                        $message->setUser($user)
                            ->setTrick($trick)
                            ->setContent($dataMessage);
                        $manager->persist($message);
                        $u++;
                    }
                }
            }
        }

        $manager->flush();
    }
}
