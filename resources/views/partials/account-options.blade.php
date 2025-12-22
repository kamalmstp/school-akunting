@foreach($accounts as $account)
<option value="{{ $account->id }}">{{ $account->code }} - {{ $account->name }}</option>
@endforeach