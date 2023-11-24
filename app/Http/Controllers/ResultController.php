<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function showResult(Request $request)
    {

        $similarCases = DB::table('data_newer1')->get();
        $toolValue = $request->input('tool', '1');
        switch ($toolValue) {
            case 'ไม่มี':
                $tool = '1';
                break;
            case 'เครื่องช่วยฟัง':
                $tool = '2';
                break;
            case 'เขียนพิมพ์สื่อสาร':
                $tool = '3';
                break;
            case 'ไม้เท้า':
                $tool = '4';
                break;
            case 'ไม้ค้ำยัน':
                $tool = '5';
                break;
            case 'ขาเทียม':
                $tool = '6';
                break;
            case 'วิลแชร์':
                $tool = '7';
                break;
            default:
                $tool = '3.5';
        }
        $attributeWeights = [
            'gender' => 2.8,
            'education' => 5,
            'status_' => 2.8,
            'dis_type' => 4.1,
            'tool' => 4.7,
            'keeper' => 4.6,
            'invest' => 4.1,
            'loan' => 2.4,
            'hobby' => 10,
            'aptitude' => 9.5,
            'commute' => 6.3,
        ];

        $sumOfWeights = array_sum($attributeWeights);

        $currentProblem = [
            'gender' => $request->input('gender'),
            'education' => $request->input('education'),
            'status_' => $request->input('status_'),
            'dis_type' => $request->input('dis_type'),
            'tool' => $tool ?? "1",
            'keeper' => $request->input('keeper') ?? "ไม่มี",
            'invest' => $request->input('invest') ?? "0",
            'loan' => $request->input('loan') ?? "0",
            'hobby' => $request->input('hobby') ?? [],
            'aptitude' => $request->input('aptitude') ?? [],
            'commute' => $request->input('commute') ?? "ทำงานที่บ้าน (WFH)",
        ];

        $uniqueCases = [];
        $elementsToRemove = [
            'ขับรถรับจ้าง',
            'ช่างซ่อมรถ, ช่างตัดผม',
            'ช่างไฟฟ้าและเขียนแบบAutocad',
            'ทำงานตรวจสอบและวิเคราะห์',
            'ทำเอกสาร',
            'นักลงทุน',
            'นักวาดภาพ',
            'พนักงานส่งของเคอรี่',
            'พนักงานเสิร์ฟอาหาร',
            'รับจ้างขายของ หรือทำความสะอาด',
            'รับจ้างซ่อมเครื่องยนต์',
            'รับจ้างทำการเกษตร',
            'รับจ้างส่งของ',
            'แม่บ้าน',
            'เกษตรกร',
            'เย็บผ้า ปักผ้า',
            'แม่ค้าเบเกอรี่ออนไลน์',
            'โปรแกรมเมอร์',
            'ไรเดอร์ส่งอาหาร',
            'ขับแท็กซี่',
            'พนักงานโรงงาน'
        ];

        $similarCasesArray = [];
        foreach ($similarCases as $case) {
            $caseDetails = json_decode(json_encode($case), true);
            $retrievedCase = (array) $caseDetails;

            $similarity = $this->calculateSimilarity($currentProblem, $retrievedCase, $attributeWeights);

            $similarCasesArray[] = [
                'similarity' => $similarity,
                'caseDetails' => $caseDetails,
            ];
        }

        usort($similarCasesArray, function ($a, $b) {
            return $b['similarity'] <=> $a['similarity'];
        });

        $topSimilarCases = array_slice($similarCasesArray, 0, 5);

        foreach ($topSimilarCases as $case) {
            if ($request->input('dis_type') == 9 && in_array($case['caseDetails']['occupation'], $elementsToRemove)) {
                continue;
            }
            if ($request->input('dis_type') == 4 && $case['caseDetails']['occupation'] == 'นักดนตรี') {
                continue;
            }
            if (!in_array($case['caseDetails']['occupation'], $uniqueCases)) {
                $uniqueCases[] = $case['caseDetails']['occupation'];
            }
        }

        $nextSimilarCases = array_slice($similarCasesArray, 5);
        //สำหรับเทส API ค่าดังนี้
        // return response()->json([
        //     'uniqueCases' => $uniqueCases,
        //     'topSimilarCases' => $topSimilarCases,
        //     'nextSimilarCases' => $nextSimilarCases,
        //     'sumOfWeights' => $sumOfWeights,
        //     'elementsToRemove' => $elementsToRemove,
        //     'currentProblem' => $currentProblem,
        // ]);
        return view('result', [
            'uniqueCases' => $uniqueCases,
            'topSimilarCases' => $topSimilarCases,
            'nextSimilarCases' => $nextSimilarCases,
            'sumOfWeights' => $sumOfWeights,
            'elementsToRemove' => $elementsToRemove
        ]);


    }

    private function calculateSimilarity($currentProblem, $retrievedCase, $attributeWeights)
    {
        $similarity = 0;
        foreach ($attributeWeights as $attribute => $weight) {

            if ($attribute === 'hobby' || $attribute === 'aptitude' || $attribute === 'commute') {
                if (isset($retrievedCase[$attribute])) {
                    $retrievedCase[$attribute] = explode(", ", $retrievedCase[$attribute]);
                }
            }
            if (is_array($currentProblem[$attribute])) {
                $currentValues = $currentProblem[$attribute];
                $retrievedValues = $retrievedCase[$attribute];
                $matchingValues = array_intersect($currentValues, $retrievedValues);
                $attributeSimilarity = (2 * count($matchingValues)) / (count($currentValues) + count($retrievedValues));
            } else if ($attribute === 'education') {
                $currentValues = $currentProblem[$attribute];
                $retrievedValues = $retrievedCase[$attribute];
                $attributeSimilarity = 1 - (abs($currentValues - $retrievedValues) / 7);

            } else if ($attribute === 'dis_type') {
                $currentValues = $currentProblem[$attribute];
                $retrievedValues = $retrievedCase[$attribute];
                $attributeSimilarity = 1 - (abs($currentValues - $retrievedValues) / 9);

            } else if ($attribute === 'tool') {
                $currentValues = $currentProblem[$attribute];
                $retrievedValues = $retrievedCase[$attribute];
                $attributeSimilarity = 1 - (abs($currentValues - $retrievedValues) / 7);

            } else if ($attribute === 'invest') {
                $currentValues = $currentProblem[$attribute];
                $retrievedValues = $retrievedCase[$attribute];
                $attributeSimilarity = 1 / (1 + abs($currentValues - $retrievedValues));

            } else if ($attribute === 'loan') {
                $currentValues = $currentProblem[$attribute];
                $retrievedValues = $retrievedCase[$attribute];
                $attributeSimilarity = 1 / (1 + abs($currentValues - $retrievedValues));

            } else {
                $attributeSimilarity = ($currentProblem[$attribute] == $retrievedCase[$attribute]) ? 1 : 0;
            }

            $similarity += $weight * $attributeSimilarity;

        }

        return $similarity;
    }

    private function convertToNumeric($value)
    {

        return is_numeric($value) ? $value : 0;
    }

}
