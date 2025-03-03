@include('common.topbar')

<style>
    #queries {
        width: 100%;
        height: 200px;
    }
</style>

<div>
    <h2>Percona online schema change</h2>
    <form action="{{ route('percona.show') }}" method="post">
        @csrf
        <label for="queries">Queries:</label><br/>
        <textarea id="queries" name="queries">{{$queries ?: 'ALTER TABLE `users` CHANGE `visible_mail` `visible_mail` VARCHAR(65)  CHARACTER SET utf8mb3  COLLATE utf8mb3_general_ci  NOT NULL;'}}</textarea>
        <br/><br/>
        <table>
            <tr>
                <td>alter-foreign-keys-method</td>
                <td>
                    <select name="alter-foreign-keys-method">
                        <option value="auto">auto (recommended)</option>
                        <option value="rebuild_constraints">rebuild_constraints</option>
                        <option value="drop_swap">drop_swap</option>
                        <option value="none">none</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>analyze-before-swap</td>
                <td>
                    <select name="analyze-before-swap">
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>check-alter</td>
                <td>
                    <select name="check-alter">
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>check-foreign-keys</td>
                <td>
                    <select name="check-foreign-keys">
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>check-unique-key-change</td>
                <td>
                    <select name="check-unique-key-change">
                        <option value="yes">yes</option>
                        <option value="no">no</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>drop-old-table (if possible)</td>
                <td>
                    <select name="drop-old-table">
                        <option value="no">no</option>
                        <option value="yes">yes</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>null-to-not-null</td>
                <td>
                    <input name="null-to-not-null" type="checkbox">
                </td>
            </tr>
            <tr>
                <td>print (only in dry-run)</td>
                <td>
                    <input name="print" type="checkbox">
                </td>
            </tr>
            <tr>
                <td>max-load</td>
                <td>
                    <input name="max-load" type="text" value="Threads_running=25">
                </td>
            </tr>
            <tr>
                <td>critical-load</td>
                <td>
                    <input name="critical-load" type="text" value="Threads_running=50">
                </td>
            </tr>
        </table>
        <br/>
        <button type="submit">Generate</button>
    </form>
    @if(isset($commands))
        <h2>Commands</h2>
        <code id="commands">
            @foreach($commands as $command)
                {{ $command }} <br/>
            @endforeach
        </code>
        <br/>
        <button type="submit" onclick="run(false);">Dry-run</button>
        <button type="submit" onclick="run(true);">Execute</button>
    @endif
</div>
<script>
    function run(execute = false) {
        const commands = {{ Js::from($commands ?? []) }};
        const url = '{{ route('percona.run') }}';
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ commands })
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
