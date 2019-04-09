<?php

namespace App\ProfileBundle\Command;

use App\ProfileBundle\Entity\Profile;
use App\ProfileBundle\Service\RedisService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddProfilesCommand extends Command
{
    public const BATCH_SIZE = 100;
    public const COUNT_RECORDS_ON_REDIS = 1000;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var RedisService
     */
    private $redis;

    /**
     * @var
     */
    private $redisListProfiles;

    /**
     * AddProfilesCommand constructor.
     *
     * @param EntityManagerInterface $em
     * @param RedisService $redis
     * @param $redisListProfiles
     */
    public function __construct(EntityManagerInterface $em, RedisService $redis, $redisListProfiles)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->redis = $redis;
        $this->redisListProfiles = $redisListProfiles;
    }

    protected function configure()
    {
        $this->setName('amo:media:add:profiles')
            ->setDescription('Creates profiles and stores them in the database')
            ->setHelp('AddProfilesCommand...');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $profiles = $this->redis->lrange($this->redisListProfiles, 1, -1);

        $lTrimEnd = (count($profiles) + 1) - self::COUNT_RECORDS_ON_REDIS;
        $i = 0;

        if (count($profiles) > 0) {
//            $commandElastica = $this->getApplication()->find('fos:elastica:populate');

            foreach ($profiles as $item) {
                $profileArr = json_decode($item, true);

                $profile = new Profile();
                $profile->setFirstname($profileArr['firstname'])
                    ->setLastname($profileArr['lastname'])
                    ->setPhonenumber($profileArr['phonenumber']);

                $this->entityManager->persist($profile);

                if (($i % self::BATCH_SIZE) === 0) {
                    $this->entityManager->flush();
                    $this->entityManager->clear();
                }
                $i++;
            }

            $this->entityManager->flush();
            $this->entityManager->clear();
            $this->redis->ltrim($this->redisListProfiles, 1, $lTrimEnd);
//            $commandElastica->run($input, $output);

            $output->write("Created $i profiles");
        }

    }
}
