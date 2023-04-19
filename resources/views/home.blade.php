@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"
    integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous">
</script>
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Hacer un pago </div>
                    @if ($errors->has('cancel'))
                        <div class="alert alert-danger">
                            {{ $errors->first('cancel') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success')['payment'] }}
                        </div>
                    @endif
                    <div class="card-body">
                        <form action="{{ route('pay') }}" method="post" id="payment_form">
                            @csrf
                            <div class="row">
                                <div class="col-auto">
                                    <label for="">How much you to pay ?</label>
                                    <input type="text" type="number" min="5" step="0.01" class="form-control"
                                        name="value" value="{{ mt_rand(100, 1000) / 100 }}">
                                    <small class="fomr-text text-muted">
                                        Use values with up to decimal positions,
                                        using dot "."
                                    </small>
                                </div>
                                <div class="col-auto">
                                    <label for="">Currency</label>
                                    <select name="currency" class="custom-select" required id="">
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->iso }}">{{ strtoupper($currency->iso) }}</option>
                                        @endforeach

                                    </select>
                                </div>
                                <div class="row mt-3">
                                    <div class="col">
                                        <label for="">select the desiredpayment platfomr</label>
                                        <div class="form-group" id="toggler">
                                            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                                @foreach ($PaymentPlatforms as $paymentPlatform)
                                                    <label data-target="#{{ $paymentPlatform->name }}Collapse"
                                                        data-toggle="collapse"
                                                        class="btn btn-outline-secondary rounded m-2 p-1">
                                                        <input type="radio" name="payment_platform"
                                                            value="{{ $paymentPlatform->id }}" required>
                                                        <img class="img-thumbnail"
                                                            src="{{ asset($paymentPlatform->image) }}">
                                                    </label>
                                                @endforeach

                                            </div>
                                            @foreach ($PaymentPlatforms as $paymentPlatform)
                                                <div id="{{ $paymentPlatform->name }}Collapse" class="collapse"
                                                    data-parent="#toggler">

                                                    @includeIf(
                                                        'components.' .
                                                            strtolower($paymentPlatform->name) .
                                                            '-collapse')
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>


                                </div>


                            </div>
                            <div class="text-center mt-3">
                                <button type="submit" id="payButton" class="btn btn-primary btn-lg">Pay</button>
                            </div>
                        </form>

                        <!-- Modal -->
                        {{-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                            Open Modal
                          </button>
                        <div class="modal fade" id="myModal" tabindex="-1" role="dialog"
                            aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        ...
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-primary">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div> --}}


                        {{ __('You are logged in!') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
