<?php

/*  $name = "Africa";
  $avgAge = 19.7;
  $avgDailyIncomeInUSD = 4;
  $avgDailyIncomePopulation = 0.73;

  $periodType = "days";
  $timeToElapse = 38;
  $reportedCases = 2747;
  $population = 92931687;
  $totalHospitalBeds = 678874;*/

  $name;
  $avgAge;
  $avgDailyIncomeInUSD;
  $avgDailyIncomePopulation;

  $periodType;
  $timeToElapse;
  $reportedCases;
  $population;
  $totalHospitalBeds;

  $currentlyInfected;
  $infectionsByRequestedTime;
  $severeCasesByRequestedTime;
  $hospitalBedsByRequestedTime;
  $casesForICUByRequestedTime;
  $casesForVentilatorsByRequestedTime;
  $dollarsInFlight;

  $estimatedPeriod;

function estimatedDays($type){

  if($type == "days")
  $calcEstimatedPeriod = $GLOBALS['timeToElapse'];
  else if($type == "weeks")
  $calcEstimatedPeriod = $GLOBALS['timeToElapse'] * 7;
  else if($type == "month")
  $calcEstimatedPeriod = $GLOBALS['timeToElapse'] * 30;
  else if($type == "year")
  $calcEstimatedPeriod = $GLOBALS['timeToElapse'] * 30 * 12;
  else 
  $calcEstimatedPeriod = $GLOBALS['timeToElapse'];

  
  return $calcEstimatedPeriod;
}


function assessmentOne($type1){
  
  if($type1 == "impact")
  $likelyToBeInfected = 10;
  else if($type1 == "severe")
  $likelyToBeInfected = 50;
  else 
  $likelyToBeInfected = 10;

//  $GLOBALS['estimatedPeriod'] = intval(estimatedDays($GLOBALS['periodType']));

  if($GLOBALS['periodType'] == "days")
  $GLOBALS['estimatedPeriod'] = $GLOBALS['timeToElapse'];
    else if($GLOBALS['periodType'] == "weeks")
    $GLOBALS['estimatedPeriod'] = $GLOBALS['timeToElapse'] * 7;
    else if($GLOBALS['periodType'] == "month")
    $GLOBALS['estimatedPeriod'] = $GLOBALS['timeToElapse'] * 30;
    else if($GLOBALS['periodType'] == "year")
    $GLOBALS['estimatedPeriod'] = $GLOBALS['timeToElapse'] * 30 * 12;
    else 
    $GLOBALS['estimatedPeriod'] = $GLOBALS['timeToElapse'];


  $infectionsPerThreeDays = 2;
  $periodPerThreeDays = $GLOBALS['estimatedPeriod'] / 3;
  $estimatedPeriodPerThreeDays = (int) $periodPerThreeDays;


  $numberCurrentlyInfected = $GLOBALS['reportedCases'] * $likelyToBeInfected;  
  $GLOBALS['currentlyInfected'] = (int) $numberCurrentlyInfected;

  $numberInfectionsByRequestedTime = $GLOBALS['currentlyInfected'] * (int) pow($infectionsPerThreeDays, $estimatedPeriodPerThreeDays);
  $GLOBALS['infectionsByRequestedTime'] = (int) $numberInfectionsByRequestedTime;

}


function assessmentTwo(){

  $numberSevereCasesByRequestedTime = 0.15 * $GLOBALS['infectionsByRequestedTime'];
  $GLOBALS['severeCasesByRequestedTime'] = (int) $numberSevereCasesByRequestedTime;


  $availableBeds = intval(0.35 * $GLOBALS['totalHospitalBeds']);

  $numberHospitalBedsByRequestedTime = $availableBeds - $GLOBALS['severeCasesByRequestedTime'];
  $GLOBALS['hospitalBedsByRequestedTime'] = (int) $numberHospitalBedsByRequestedTime;

}


function assessmentThree(){

    $GLOBALS['casesForICUByRequestedTime'] = (int) (0.05 * $GLOBALS['infectionsByRequestedTime']);
    $GLOBALS['casesForVentilatorsByRequestedTime'] = (int)(0.02 * $GLOBALS['infectionsByRequestedTime']);
    $GLOBALS['dollarsInFlight'] = intval(($GLOBALS['infectionsByRequestedTime'] * $GLOBALS['avgDailyIncomeInUSD'] * $GLOBALS['avgDailyIncomePopulation']) / $GLOBALS['estimatedPeriod']);
}


function severeImpact(){
  assessmentOne("severe");
  assessmentTwo();
  assessmentThree();
  
  return outputAssesssment();
}


function impact(){
  assessmentOne("impact");
  assessmentTwo();
  assessmentThree();
  
  return outputAssesssment();
}


function outputAssesssment(){

  return array(
      "currentlyInfected" => $GLOBALS['currentlyInfected'],
      "infectionsByRequestedTime" => $GLOBALS['infectionsByRequestedTime'],
      "severeCasesByRequestedTime" => $GLOBALS['severeCasesByRequestedTime'],
      "hospitalBedsByRequestedTime" => $GLOBALS['hospitalBedsByRequestedTime'],
      "casesForICUByRequestedTime" => $GLOBALS['casesForICUByRequestedTime'],
      "casesForVebtilatorsByRequestedTime" => $GLOBALS['casesForVentilatorsByRequestedTime'],
      "dollarsInFlight" => $GLOBALS['dollarsInFlight']
    );

}


function covid19ImpactEstimator($data)
{

  $primaryData = json_decode(file_get_contents('php://input'), true);
//  $primaryData = json_decode($data, true);


  $GLOBALS['name'] = $data["region"]['name'];
  $GLOBALS['avgAge'] = $data["region"]["avgAge"];
  $GLOBALS['avgDailyIncomeInUSD'] = $data["region"]["avgDailyIncomeInUSD"];
  $GLOBALS['avgDailyIncomePopulation'] = $data["region"]['avgDailyIncomePopulation'];
  $GLOBALS['periodType'] = $data['periodType'];
  $GLOBALS['timeToElapse'] = $data["timeToElapse"];
  $GLOBALS['reportedCases'] = $data["reportedCases"];
  $GLOBALS['population'] = $data["population"];
  $GLOBALS['totalHospitalBeds'] = $data["totalHospitalBeds"];

  $output = array(
    "data" => $data,
//    "estimate" => array(
    "impact" => impact(),
    "severeImpact" => severeImpact()
//        )
      );
  
    echo json_encode($output);

//  return $data;
}




  



//    covid19ImpactEstimator("hi");
