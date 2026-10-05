<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingShowRequest;
use App\Http\Requests\StorePassengerDetailRequest;
use App\Interfaces\FlightRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    private FlightRepositoryInterface $flightRepository;
    private TransactionRepositoryInterface $transactionRepository;

    public function __construct(
        FlightRepositoryInterface $flightRepository,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->flightRepository = $flightRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function booking(Request $request, $flightNumber)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());
        return redirect()->route('booking.chooseSeat', ['flightNumber' => $flightNumber]);
    }

    public function chooseSeat(Request $request, $flightNumber)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $flight = $this->flightRepository->getFlightByFlightNumber($flightNumber);
        $tier = $flight->classes?->find($transaction['flight_class_id']);
        return view('pages.booking.choose-seat', compact('transaction', 'flight', 'tier'));
    }

    public function confirmSeat(Request $request, $flightNumber)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());
        return redirect()->route('booking.passengerDetails', ['flightNumber' => $flightNumber]);
    }

    public function passengerDetails(Request $request, $flightNumber)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $flight = $this->flightRepository->getFlightByFlightNumber($flightNumber);
        $tier = $flight->classes->find($transaction['flight_class_id']);

        return view('pages.booking.passenger-details', compact('transaction', 'flight', 'tier'));
    }

    public function savePassengerDetails(StorePassengerDetailRequest $request, $flightNumber)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());
        return redirect()->route('booking.checkout', ['flightNumber' => $flightNumber]);
    }

    public function checkout($flightNumber)
    {
        $transaction = $this->transactionRepository->getTransactionDataFromSession();
        $flight = $this->flightRepository->getFlightByFlightNumber($flightNumber);
        $tier = $flight->classes->find($transaction['flight_class_id']);
        return view('pages.booking.checkout', compact('transaction', 'flight', 'tier'));
    }

    public function payment(Request $request, $flightNumber = null)
    {
        $this->transactionRepository->saveTransactionDataToSession($request->all());
        $transaction = $this->transactionRepository->saveTransaction($this->transactionRepository->getTransactionDataFromSession());

        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production', config('midtrans.isProduction', false));
        \Midtrans\Config::$isSanitized = config('midtrans.is_sanitized', config('midtrans.isSanitized', true));
        \Midtrans\Config::$is3ds = config('midtrans.is_3ds', config('midtrans.is3ds', true));
        \Midtrans\Config::$clientKey = config('midtrans.client_key');

        $params = [
            'transaction_details' => [
                'order_id' => $transaction->code,
                'gross_amount' => (int) $transaction->grandtotal,
            ]
        ];

        $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

        return redirect($paymentUrl);
    }

    public function success(Request $request)
    {
        $transaction = $this->transactionRepository->getTransactionByCode($request->order_id);
        if (!$transaction) {
            return redirect()->route('home');
        }

        return view('pages.booking.success', compact('transaction'));
    }

    public function checkBooking()
    {
        return view('pages.booking.check-booking');
    }

    public function showBooking(BookingShowRequest $request)
    {
        $transaction = $this->transactionRepository->getTransactionByCodePhone($request->code, $request->phone);
        if (!$transaction) {
            return redirect()->back()->with('error', 'Booking not found. Please check your booking code and phone number');
        }

        return view('pages.booking.detail', compact('transaction'));
    }
}
