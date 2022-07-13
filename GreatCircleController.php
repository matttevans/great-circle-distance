<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GreatCircleController
{

    // Mean radius of Earth = 6371.0088 km

    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     */
    public function haversine(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'originLatitude' => 'required|numeric',
            'originLongitude' => 'required|numeric',
            'destinationLatitude' => 'required|numeric',
            'destinationLongitude' => 'required|numeric',
        ]);

        if ($validator->passes()) {

            $fromLatitude = deg2rad($request->originLatitude);
            $fromLongitude = deg2rad($request->originLongitude);
            $toLatitude = deg2rad($request->destinationLatitude);
            $toLongitude = deg2rad($request->destinationLongitude);
            $bodyRadius = $request->bodyRadius ?? 6371000;

            $latitudeDelta = $toLatitude - $fromLatitude;
            $longitudeDelta = $toLongitude - $fromLongitude;

            $angle = 2 * asin(sqrt(pow(sin($latitudeDelta / 2), 2)
                    + cos($fromLatitude)
                    * cos($toLatitude)
                    * pow(sin($longitudeDelta / 2), 2)));

            return response()->json([
                'distance' => $angle * $bodyRadius
            ]);
        }
        else {
            return $validator->errors();
        }
    }

    /**
     * Calculates the great-circle distance between two points, with
     * the Vincenty formula.
     */
    public function vincenty(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'originLatitude' => 'required|numeric',
            'originLongitude' => 'required|numeric',
            'destinationLatitude' => 'required|numeric',
            'destinationLongitude' => 'required|numeric',
        ]);

        if ($validator->passes()) {

            $fromLatitude = deg2rad($request->originLatitude);
            $fromLongitude = deg2rad($request->originLongitude);
            $toLatitude = deg2rad($request->destinationLatitude);
            $toLongitude = deg2rad($request->destinationLongitude);
            $bodyRadius = $request->bodyRadius ?? 6371000;

            $longitudeDelta = $toLongitude - $fromLongitude;

            $a = pow(cos($toLatitude) * sin($longitudeDelta), 2)
                + pow(
                    cos($fromLatitude)
                    * sin($toLatitude)
                    - sin($fromLatitude)
                    * cos($toLatitude)
                    * cos($longitudeDelta), 2
                );
            $b = sin($fromLatitude) * sin($toLatitude) + cos($fromLatitude) * cos($toLatitude) * cos($longitudeDelta);

            $angle = atan2(sqrt($a), $b);

            return response()->json([
                'distance' => $angle * $bodyRadius
            ]);
        }
        else {
            return $validator->errors();
        }
    }

    /**
     * Returns the azimuth given two pairs of latitude/longitude points
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     */
    public function azimuth(Request $request) {

        $data = $request->all();

        $validator = Validator::make($data, [
            'originLatitude' => 'required|numeric',
            'originLongitude' => 'required|numeric',
            'destinationLatitude' => 'required|numeric',
            'destinationLongitude' => 'required|numeric',
        ]);

        if ($validator->passes()) {

            $fromLatitude = deg2rad($request->originLatitude);
            $fromLongitude = deg2rad($request->originLongitude);
            $toLatitude = deg2rad($request->destinationLatitude);
            $toLongitude = deg2rad($request->destinationLongitude);
            $bodyRadius = $request->bodyRadius ?? 6371000;

            $b = acos(cos(90 - $toLatitude)
                * cos (90 - $fromLatitude)
                + sin (90 - $toLatitude)
                * sin (90 - $fromLatitude)
                * cos ($toLongitude - $fromLongitude));

            $a = asin(
                sin(90 - $toLatitude)
                * sin($toLongitude - $fromLongitude)
                / sin ($b));

            return response()->json([
                'azimuth' => $a
            ]);
        }
        else {
            return $validator->errors();
        }
    }

    /**
     * Returns half the circumference of a specific body given the radius of said body.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Support\MessageBag
     */
    public function distanceToPoles(Request $request) {

        $data = $request->all();

        $validator = Validator::make($data, [
            'bodyRadius' => 'required|numeric',
        ]);

        if ($validator->passes()) {

            $bodyRadius = $request->bodyRadius ?? 6371000;

            return response()->json([
                'halfCircumference' => M_PI * pow($bodyRadius, 2)
            ]);
        }
        else {
            return $validator->errors();
        }
    }
}
