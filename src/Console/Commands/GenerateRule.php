<?php

namespace Wubbleyou\AccessControlChecker\Console\Commands;

class GenerateRule extends StubCommand {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wubbleyou:generate-acc-rule {rule}';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates Wubbleyou\AccessControlChecker rule';

    /**
     * Where the generated file is saved.
     *
     * @var string
     */
    protected $fileLocation = 'app/ACCRules';

    /**
     * The stub filename to base the generated file from.
     *
     * @var string
     */
    protected $stubFileName = 'RuleStub';

    public function handle() {
        $this->fileName = $this->argument('rule');
        $this->stubVariables = [
            'CLASS_NAME' => $this->fileName,
        ];

        if($this->generateStub())
            return $this->info("Generated ACC rule: " . $this->path . '/' . $this->fileName . '.php');
        
        $this->error("You have already generated a ACC rule with that filename: " . $this->fileName . '.php');
    }
}