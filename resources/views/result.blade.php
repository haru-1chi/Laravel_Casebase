<!DOCTYPE html>
<html>

<head>
    <title>Result Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{ asset('css/stylev4.css') }}">
</head>

<body>
    <nav class="navbar">
        <div class="container-nav">
            <a class="navbar-brand" href="{{ route('index') }}">CasebasedOccupation</a>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('index') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">About me</a>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container">
        <h1 class="center-text">ผลการวิเคราะห์</h1>
        <h2 class="center-text">อันดับอาชีพที่แนะนำ</h2>
        @if (!empty($uniqueCases))
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>อันดับ</th>
                            <th>อาชีพที่แนะนำ</th>
                            <th>ค่าคล้ายคลึง</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $counter = 1;
                        @endphp
                        @foreach ($uniqueCases as $case)
                            <tr>
                                <td>
                                    {{ $counter }}
                                </td>
                                <td>
                                    {{ $case }}
                                </td>
                                <td>
                                    @foreach ($topSimilarCases as $topCase)
                                        @if ($topCase['caseDetails']['occupation'] === $case)
                                            {{ number_format((($topCase['similarity'] / $sumOfWeights) * 100), 2) }}%
                                            @break
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                            @php
                                $counter++;
                            @endphp
                        @endforeach

                        @foreach ($nextSimilarCases as $nextCase)
                            @if ($counter > 5)
                                @break
                            @endif
                            @if (!in_array($nextCase['caseDetails']['occupation'], $uniqueCases) && !in_array($nextCase['caseDetails']['occupation'], $elementsToRemove))
                                <tr>
                                    <td>
                                        {{ $counter }}
                                    </td>
                                    <td>
                                        {{ $nextCase['caseDetails']['occupation'] }}
                                    </td>
                                    <td>
                                        {{ number_format((($nextCase['similarity'] / $sumOfWeights) * 100), 2) }}%
                                    </td>
                                </tr>
                                @php
                                    $counter++;
                                    $uniqueCases[] = $nextCase['caseDetails']['occupation'];
                                @endphp
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p>ไม่พบกรณีที่ใกล้เคียง</p>
        @endif
        <br>
        <div class="btn-container">
            <a href="{{ route('index') }}" class="btn btn-primary">กลับสู่หน้าหลัก</a>
        </div>
    </div>
</body>

</html>
