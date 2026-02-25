<?php

namespace App\Http\Controllers;

use App\Interfaces\AirlineRepositoryInterface;
use App\Interfaces\AirportRepositoryInterface;
use App\Interfaces\FlightRepositoryInterface;
use Illuminate\Http\Request;

class FlightController extends Controller
{
    private AirlineRepositoryInterface $airlineRepository;
    private FlightRepositoryInterface $flightRepository;
    private AirportRepositoryInterface $airportRepository;

    public function __construct(AirlineRepositoryInterface $airlineRepository, FlightRepositoryInterface $flightRepository, AirportRepositoryInterface $airportRepository)
    {
        $this->airlineRepository = $airlineRepository;
        $this->flightRepository = $flightRepository;
        $this->airportRepository = $airportRepository;
    }
    public function index(Request $request)
    {
        $departure = $this->airportRepository->getAirportByIataCode($request->departure);

        $arrival = $this->airportRepository->getAirportByIataCode($request->arrival);

        $flights = $this->flightRepository->getAllFlights([
            'departure' => $departure->id ?? null,
            'arrival' => $arrival->id ?? null,
            'date' => $request->date ?? null
        ]);

        $airlines = $this->airlineRepository->getAllAirlines();

        // menggunakan map dan unique id untuk mendapatkan valuenya
        $flightFacilities = $flights->flatMap(function ($flight) {
            return $flight->classes->flatMap(function ($class) {
                return $class->facilities;
            });
        })->unique('id')->values();

        // menghitung semua jumlah transit
        $transitCounts = $flights->map(function ($flight) {
            return max($flight->segments->count() - 2, 0);
        })->unique()->sort()->values();

        return view('pages.flight.index', compact('flights', 'airlines', 'flightFacilities', 'transitCounts'));
    }
}
