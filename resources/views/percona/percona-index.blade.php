@include('common.topbar')

<style>
    #perconaQueries {
        width: 100%;
        height: 200px;
    }

    .perconaCommand {
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }

    #perconaOutput {
        white-space: pre-wrap;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
</style>

<div>
    <h2>Percona online schema change</h2>
    <form action="{{ route('percona.show') }}" method="post">
        @csrf
        <label for="queries">Queries:</label><br/>
        <textarea id="perconaQueries" name="queries">{{$queries}}</textarea>
        <br/><br/>
        <table>
            @foreach ($config as $name => $properties)
                <tr>
                    <td>{{$name}}</td>
                    <td>
                        @switch($properties['type'])
                            @case('enum')
                                <select name="{{$name}}">
                                    @foreach ($properties['admitted_values'] as $value => $text)
                                        <option
                                                value="{{$value}}"
                                                {{ (isset($options[$name]) && $options[$name] === $value) ? 'selected' : '' }}>
                                            {{$text}}
                                        </option>
                                    @endforeach
                                </select>
                                @break
                            @case('yesno')
                                <select name="{{$name}}">
                                    <option value="yes" {{ (isset($options[$name]) && $options[$name] === 'yes') ? 'selected' : '' }}>yes</option>
                                    <option value="no" {{ (isset($options[$name]) && $options[$name] === 'no') ? 'selected' : '' }}>no</option>
                                </select>
                                @break
                            @case('flag')
                                <select name="{{$name}}">
                                    <option value="on" {{ (isset($options[$name]) && $options[$name] === 'on') ? 'selected' : '' }}>on</option>
                                    <option value="off" {{ (isset($options[$name]) && $options[$name] === 'off') ? 'selected' : '' }}>off</option>
                                </select>
                                @break
                            @case('string')
                                <input name="{{$name}}" type="text" value="{{$options[$name]}}">
                                @break
                        @endswitch
                    </td>
                </tr>
            @endforeach
        </table>
        <br/>
        <button type="submit">Generate</button>
    </form>
    @if(isset($commands))
        <h2>Commands</h2>
        @foreach($commands as $command)
            <code class="perconaCommand">{{$command}}</code>
        @endforeach
        <br/>
        <br/>
        <button type="submit" onclick="run(false);">Dry-run</button>
        <button type="submit" onclick="run(true);">Execute</button>
    @endif
    @if(isset($commands))
        <h2>Output</h2>
        <code id="perconaOutput"></code>
        <br/>
    @endif
</div>
<script>
    function run(execute = false) {
        document.getElementById('perconaOutput').innerHTML = '';
        const url = '{{ route('percona.run') }}';
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                queries: '{{$queries}}',
                options: @json($options),
                execute: execute
            })
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => {
                console.error(error);
            });
    }
</script>

@include('common.common')
