<?php

namespace App\Http\Controllers;

use App\Jobs\RunSchemaChange;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Bus;
use PtOscCommandGenerator\Generator;
use PtOscCommandGenerator\Exceptions\ParserException;
use Throwable;

class PerconaController extends Controller
{
    public function show(Request $request): View
    {
        $queries = $request->input('queries', '') ?? '';
        $generator = new Generator($queries);
        $commands = $generator->getCommands();
        return view('percona.percona-index', [
            'queries' => $queries,
            'commands' => $commands,
        ]);
    }

    public function generateCommands(Request $request): array
    {
        $queries = $request->input('queries', '') ?? '';
        try {
            $generator = new Generator($queries);
        } catch (ParserException $pe) {
            return [
                'error' => $pe->getMessage(),
            ];
        }

        return [
            'commands' => $generator->getCommands(),
        ];
    }

    public function runCommands(Request $request)
    {
        $commands = $request->input('commands');

        // TODO: choose the better approach: separate jobs or chain
        /*
        foreach ($commands as $command) {
            RunPerconaCommand::dispatch($command);
        }
        */
        Bus::chain(
            collect($commands)->map(fn($c) => new RunSchemaChange($c))
        )->catch(function (Throwable $e) {
            // TODO: handle failure
        })->dispatch();

        return ['scheduled' => true];
        //return redirect('/percona/monitor');
    }
}
