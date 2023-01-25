<?php

namespace App\Command;

use App\Repository\AnnonceRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test',
    description: 'CronTask de test by Nico et Emeric',
)]
class TestCommand extends Command
{
    public ?AnnonceRepository $annonceRepository = null;

    public function __construct(AnnonceRepository $annonceRepository)
    {
        parent::__construct();
        $this->annonceRepository = $annonceRepository;
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $annonces = $this->annonceRepository->findAll();
        $inOut = new SymfonyStyle($input, $output);
        $inOut->note('salut salut !');
        $inOut->note($annonces[0]->getTitle());
        $annonces[0]->setTitle('Prout');
        $this->annonceRepository->save($annonces[0], true);
        $inOut->note($annonces[0]->getTitle());


        /*        $arg1 = $input->getArgument('arg1');

                if ($arg1) {
                    $io->note(sprintf('You passed an argument: %s', $arg1));
                }*/

/*        if ($input->getOption('option1')) {
            // ...
        }*/

/*        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');*/

        return Command::SUCCESS;
    }
}
