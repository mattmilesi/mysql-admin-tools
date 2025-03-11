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
use PtOscCommandGenerator\Exceptions\ParserException;
use PtOscCommandGenerator\Option;
use PtOscCommandGenerator\StatementParser;
use Throwable;

class PerconaController extends Controller
{
    public function show(Request $request): View
    {
        $queries = $request->input('queries', '') ?? '';
        $options = $this->extractOptionsFromRequest($request);
        $commands = $this->getCommandsFromRawQueries($queries, $options, false, true);
        return view('percona.percona-index', [
            'config' => SchemaChangeService::SUPPORTED_OPTIONS,
            'queries' => $queries ?: 'ALTER TABLE `users` CHANGE `first_name` `first_name` VARCHAR(100)  CHARACTER SET utf8mb4  COLLATE utf8mb4_general_ci  NOT NULL;',
            'commands' => $commands,
        ]);
    }

    public function runCommands(Request $request)
    {
        // Rebuild commands to reduce risks of arbitrary RCE
        $queries = $request->input('queries', '') ?? '';
        $options = $request->input('options', []) ?? [];
        $execute = $request->input('execute', false) ?? false;
        $commands = $this->getCommandsFromRawQueries($queries, $options, $execute, false);

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

    private function extractOptionsFromRequest(Request $request): array
    {
        $options = [];
        foreach (array_keys(SchemaChangeService::SUPPORTED_OPTIONS) as $option) {
            if ($request->input($option) !== null) {
                $options[$option] = $request->input($option);
            }
        }
        return $options;
    }

    private function getCommandsFromRawQueries(string $queries, array $options = [], bool $execute = false, bool $reformat = false): array
    {
        $generator = new StatementParser($queries);
        return collect($generator->getCommands())
            ->map(function (Command $c) use ($options, $execute, $reformat) {
                foreach ($options as $option => $value) {
                    $c->setOption($option, $value);
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
