<?php

namespace App\Http\Controllers;

use App\Jobs\RunSchemaChange;
use App\Services\SchemaChange\SchemaChangeService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use PtOscCommandGenerator\Command;
use PtOscCommandGenerator\DsnOption;
use PtOscCommandGenerator\Option;
use PtOscCommandGenerator\StatementParser;
use Throwable;

class PerconaController extends Controller
{
    const string DEFAULT_QUERY = 'ALTER TABLE `users` CHANGE `first_name` `first_name` VARCHAR(100)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NOT NULL;';

    public function show(Request $request): View
    {
        $queries = $request->input('queries', '') ?? '';
        $options = $this->extractOptionsFromRequest($request);
        $scOptions = $this->generateSchemaChangeOptions($options);
        $commands = $this->generateCommandsFromRawQueries($queries, $scOptions, false, true);
        return view('percona.percona-index', [
            'config' => SchemaChangeService::SUPPORTED_OPTIONS,
            'queries' => $queries ?: self::DEFAULT_QUERY,
            'options' => $options,
            'commands' => $commands,
        ]);
    }

    public function runCommands(Request $request)
    {
        // Rebuild commands from raw queries to reduce risks of arbitrary RCE
        $queries = $request->input('queries', '') ?? '';
        $execute = $request->input('execute', false) ?? false;
        $options = $this->extractOptionsFromRequest($request);
        $scOptions = $this->generateSchemaChangeOptions($options);
        $commands = $this->generateCommandsFromRawQueries($queries, $scOptions, $execute, false);

        // TODO: choose the better approach: separate jobs or chain
        /*
        foreach ($commands as $command) {
            RunPerconaCommand::dispatch($command);
        }
        */
        Bus::chain(
            collect($commands)->map(fn($c) => new RunSchemaChange($c))
        )->catch(function (Throwable $e) {
            // TODO: handle failure?
            throw $e;
        })->dispatch();

        return [
            'scheduled' => true,
            'commands' => $commands,
        ];
        //return redirect('/percona/monitor');
    }

    private function cleanCliParam(string $param): string
    {
        return preg_replace('/[^A-Za-z0-9_=,]/', '', $param);
    }

    private function extractOptionsFromRequest(Request $request): array
    {
        $options = [];
        foreach (SchemaChangeService::SUPPORTED_OPTIONS as $option => $properties) {
            if ($request->input($option) !== null) {
                $options[$option] = $this->cleanCliParam($request->input($option));
            } else {
                $options[$option] = $properties['default_value'];
            }
        }
        return $options;
    }

    private function generateSchemaChangeOptions($options): array
    {
        $scOptions = [];
        foreach (SchemaChangeService::SUPPORTED_OPTIONS as $name => $properties) {
            if (isset($options[$name])) {
                if ($properties['type'] === 'yesno') {
                    $scOptions[$properties[$options[$name] . '_option']] = '';
                } elseif ($properties['type'] === 'flag') {
                    if ($options[$name] === 'on') {
                        $scOptions[$name] = '';
                    }
                } elseif ($properties['type'] === 'string') {
                    $scOptions[$name] = "\"{$options[$name]}\"";
                } else {
                    $scOptions[$name] = $options[$name];
                }
            }
        }
        return $scOptions;
    }

    private function generateCommandsFromRawQueries(string $queries, array $options = [], bool $execute = false, bool $reformat = false): array
    {
        $generator = new StatementParser($queries);
        return collect($generator->getCommands())
            ->map(function (Command $c) use ($options, $execute, $reformat) {
                foreach ($options as $option => $value) {
                    $c->setOption($option, $value);
                }
                if (!$execute && !$reformat) {
                    $c->setOption(Option::PRINT);
                }
                return $c
                    ->setDsnOption(DsnOption::HOST, Config::get('database.connections.target_mysql.host'))
                    ->setDsnOption(DsnOption::DATABASE, Config::get('database.connections.target_mysql.database'))
                    //->setDsnOption(DsnOption::USER, Config::get('database.connections.target_mysql.username'))
                    //->setDsnOption(DsnOption::PASSWORD, Config::get('database.connections.target_mysql.password'))
                    ->setDsnOption(DsnOption::USER, 'root')
                    ->setDsnOption(DsnOption::PASSWORD, 'root')
                    ->setMode($execute ? Command::MODE_EXECUTE : Command::MODE_DRY_RUN)
                    ->setOption('progress', 'percentage,1')
                    ->toString(!$reformat, $reformat);
            }
            )->toArray();
    }
}
