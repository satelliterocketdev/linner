<!DOCTYPE html>
<html>
<head>
  <style>
    .receipt-frame{
      padding: 0.5em 0.5em;
      margin: 1em 1em;
      border: solid 2px #000000;
    }
</style>
</head>
<body>
  <div class="receipt-frame">
    <h2 style="margin: 10px;">{{ __('receipt.receipt') }}</h2>
    <br>
    <p>{{ $settlement->client }}</p>
    <hr>
    <p>¥{{ $settlement->amount }}</p>
    <hr>
    <p>{{ __('receipt.declare') }}</p>
  </div>
</body>
</html>