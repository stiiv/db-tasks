<?php
namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputArgument;

use App\Core\Storage\GatherAnswers;

class RemoveDoubleRecord extends Command
{
	/**
	 * GatherAnswers instance
	 * @var object
	 */
	protected $answers;

	public function __construct()
	{
		$this->answers = new GatherAnswers('remove-double-record');
		parent::__construct();
	}

	protected function configure()
	{
	    $this
	        // the name of the command (the part after "bin/console")
	        ->setName('db:remove-double-record')

	        // the short description shown while running "php bin/console list"
	        ->setDescription('Remove double record from database based on table and column name and update all foreign keys (references)')

	        // the full command description shown when running the command with
	        // the "--help" option
	        ->setHelp('Removes double record from database based on table and column name and update all foreign keys (references)')

	        // clear cached info
	        ->addOption(
	        	'clear-cache', 
	        	'C', 
	        	null,
	        	'Clear input cache (default values)'
	        )
	    ;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->handleOptions($input, $output);
		$helper = $this->getHelper('question');

    	foreach ($this->getQueestions() as $info_key => $info_data) {
    		$default = isset($info_data['default']) 
    			? $this->answers->getCachedItem($info_key, $info_data['default']) 
    			: $this->answers->getCachedItem($info_key, '');
    		$format_default = $default ? sprintf('[%s] ', $default) : '';
    		$question = new Question($info_data['question'].$format_default, $default);
    		$answer = trim($helper->ask($input, $output, $question));
    		$this->answers->add($info_key, $answer);
    	}

    	var_dump($this->answers->all());
	}

	protected function handleOptions(InputInterface $input, OutputInterface $output)
	{
		if( $input->getOption('clear-cache') ) {
			$this->answers->clearCache();
			$output->writeln('<info>Cache cleared!</info>');
		}
	}

	protected function getQueestions()
	{
		return [
			'db_host' => [
				'question' => 'Please enter database host: ', 
				'default' => '127.0.0.1'
			],

			'db_name' => [
				'question' => 'Please enter database name: ', 
				'default' => ''
			],

			'db_username' => [
				'question' => 'Please enter database username: ', 
				'default' => 'root'
			],

			'db_password' => [
				'question' => 'Please enter database password: ', 
				'default' => ''
			],

			'db_charset' => [
				'question' => 'Please enter database charset: ', 
				'default' => 'utf8'
			],

			'db_collation' => [
				'question' => 'Please enter database collation: ', 
				'default' => 'utf8_unicode_ci'
			],

			'table' => [
				'question' => 'Please enter database table: ', 
				'default' => '',
			],

			'column_name' => [
				'question' => 'Please enter table column name to check double record on: ', 
				'default' => '',
			],

			'possible_references' => [
				'question' => 'Please enter possible foreign keys to update references (comma separated): ', 
				'default' => '',
			],
		];
	}
}