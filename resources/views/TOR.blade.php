<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Transcript of Record</title>
    <style>
        @page {
            size: 8.5in 13in;
            margin: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.1;
            background-color: white;
            margin: 0;
            padding: 0;
            width: 8.5in;
        }

        .container {
            position: relative;
            padding-left: 0.25in;
            padding-right: 0.25in;
            padding-top: 0.15in;
            padding-bottom: 2.5in;
            /* Increased bottom padding to make room for footer */
        }

        h4 {
            text-align: center;
            margin-top: 30px;
            font-weight: bold;
        }

        .student-info {
            display: table;
            width: 100%;
            border-spacing: 0;
            table-layout: fixed;
            padding-top: 55px;
            /* Adjusted down further */
        }

        .left-side {
            display: table-cell;
            width: 49%;
            vertical-align: top;
            padding-right: 10px;
            padding-top: 162px;
            /* Adjusted down further */
        }

        .right-side {
            display: table-cell;
            width: 51%;
            vertical-align: top;
            padding-left: 10px;
            padding-top: 40px;
            /* Adjusted down further */
        }

        .entrance-header {
            font-weight: bold;
            margin-bottom: 12px;
        }

        .data-row {
            display: flex;
            width: 120%;
        }

        .label {
            white-space: nowrap;
            padding-right: 5px;
            flex: 0 0 auto;
        }

        .left-side .label {
            width: 50px;
        }

        .right-side .label {
            width: 40px;
        }

        .underlined-value {
            display: inline-block;
            border-bottom: 1px solid black;
            padding-left: 2px;
            text-align: center;
        }

        .left-side .underlined-value {
            width: 140px;
        }

        .right-side .underlined-value {
            width: 255px;
        }

        .student-name {
            font-weight: 900;
        }

        /* 
      
        .transcript-container {
            position: relative;
            max-height: 60%;
            overflow: hidden;
        } */

        .transcript-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            padding: 0;
            height: 5in;
        }

        .transcript-table th {
            border: 2px solid black;
            text-align: center;
            font-weight: bold;
            padding: 0px;
            font-size: 9pt;
        }

        .transcript-table td {
            border-left: 1px solid black;
            border-right: 1px solid black;
            padding: 1.5px 3px;
            /* Reduced padding */
            height: 10px;
            /* Reduced height for more compression */
            font-size: 10pt;
            line-height: 0.87;
            /* Reduced line height for compression */
        }

        .transcript-table tr:first-child td {
            border-top: 1px solid black;
        }

        .transcript-table tr:last-child td {
            border-bottom: 1px solid black;
        }

        /* 
        .empty-row td {
            height: 0;
            border-left: 1px solid black;
            border-right: 1px solid black;
        } */

        .term-cell {
            text-align: left;
            vertical-align: top;
            top: 0;
        }

        .course-code {
            white-space: nowrap;
            justify-content: space-between
        }

        .course-code-prefix {
            float: left;
        }

        .course-code-number {
            float: right;
        }

        .desc-title {
            text-align: left;
        }

        .photo-container {
            width: 2in;
            height: 2in;
            padding-bottom: 5px;
            border: 1.5px solid #000;
            float: left;
            margin-right: 10px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .grading-legend {
            margin-left: 2.1in;
            font-size: 9pt;
            line-height: 1.3;
        }

        .remarks {
            clear: both;
            font-size: 12px;
        }

        .signatures {
            display: table;
            width: 100%;
            margin-top: 0.5px;
        }

        .signature-column {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
            font-size: 13px;
        }

        .reference-id {
            font-size: 25px;
            font-weight: bold;
        }

        .form-number {
            display: flex;
            justify-content: space-between;
            font-size: 10px;
        }

        #revision {
            text-align: right;
            float: right;
        }

        #effectivity {
            text-align: left;
            float: left;
        }

        .bor-ref {
            text-align: right;
            font-size: 9pt;
            margin-top: 2px;
        }

        .descriptive-title {
            text-align: center;
            letter-spacing: 2px;
            font-weight: bold;
            font-size: 9pt;
        }

        .page-break-before {
            page-break-inside: avoid;
        }

        /* Increased line spacing for college/campus header */
        .college-campus-header {
            text-align: center;
            margin-bottom: 15px;
            text-decoration: underline;
            font-weight: bold;
            font-size: 10pt;
            line-height: 1.9;
            /* Increased line spacing */
        }

        /* Updated footer positioning */
        .page-footer {
            position: fixed;
            /* Changed from absolute to fixed */
            bottom: 0;
            left: 0;
            right: 0;
            margin-top: 1px;
            /* Reduced margin-top */
            margin-bottom: 20px;
            padding: 0.35in 0.25in;
            page-break-inside: avoid;
        }



        /* Specific styling for first page footer
        .first-page-footer {
            bottom: 0.6in; 
        } */

        .graduation-info {
            text-align: center;
            font-weight: bold;
            border: none;
            position: relative;
            margin-top: auto;
            padding: 0;
        }

        @media print {
            .container {
                page-break-inside: avoid;
            }
        }
    </style>
</head>

<body>
    @php
    // Improved function to count display rows with better wrapping detection
    function countDisplayRows($record) {
    // Each record always counts as at least 1 row
    $rowCount = 1;

    // Get the descriptive title
    $title = $record->descriptive_title ?? '';

    // More accurate calculation for wrapped text
    // Assuming approximately 45 characters fit on one line in the table cell with 43% width
    $titleLength = strlen($title);
    $charsPerLine = 45;
    $estimatedLines = ceil($titleLength / $charsPerLine);

    // If title wraps to multiple lines, add additional rows
    // But subtract 1 because we already counted the first line
    if ($estimatedLines > 1) {
    $rowCount += ($estimatedLines - 1);
    }

    return $rowCount;
    }

    // Get all records sorted by academic term
    $allRecords = $student->records()
    ->with(['academicTerm', 'curricula'])
    ->orderBy('acad_term_id')
    ->get();

    // Initialize variables for pagination
    $rowsPerPage = 27; // Fixed number of rows per page for normal pages
    $firstPageMaxRows = 28; // Fewer rows on first page due to header
    $pages = [];

    // Temporary collection to build each page
    $tempPageRecords = collect([]);
    $currentPageRowCount = 0;

    // First page has header rows that take up space (college/campus header)
    $firstPageHeaderRows = 1;
    $firstPageMaxRows = $rowsPerPage - $firstPageHeaderRows - 1; // Subtract 1 more for safety
    $isFirstPage = true;

    // Track terms to handle term grouping properly
    $currentTermId = null;
    $currentTermRecords = collect([]);

    // Group records by term first
    $recordsByTerm = $allRecords->groupBy(function($record) {
    return $record->acad_term_id;
    });

    // Process each term group
    foreach ($recordsByTerm as $termId => $termRecords) {
    // Calculate total rows needed for this term
    $termRowCount = 0;
    foreach ($termRecords as $record) {
    $termRowCount += countDisplayRows($record);
    }

    // Check if adding this term would exceed current page capacity
    $availableRows = $isFirstPage ? $firstPageMaxRows : $rowsPerPage;

    if ($currentPageRowCount + $termRowCount > $availableRows) {
    // This term won't fit on current page, save current page and start new one
    if ($tempPageRecords->count() > 0) {
    $pages[] = $tempPageRecords;
    $tempPageRecords = collect([]);
    $currentPageRowCount = 0;
    $isFirstPage = false;
    }

    // If the entire term is too big for a single page, split it
    if ($termRowCount > $rowsPerPage) {
    $termPageRowCount = 0;
    $termPageRecords = collect([]);

    foreach ($termRecords as $record) {
    $recordRows = countDisplayRows($record);

    if ($termPageRowCount + $recordRows > $rowsPerPage) {
    // This record would exceed page limit
    $pages[] = $termPageRecords;
    $termPageRecords = collect([$record]);
    $termPageRowCount = $recordRows;
    } else {
    // Add record to current term page
    $termPageRecords->push($record);
    $termPageRowCount += $recordRows;
    }
    }

    // Add last batch of term records if any remain
    if ($termPageRecords->count() > 0) {
    $tempPageRecords = $termPageRecords;
    $currentPageRowCount = $termPageRowCount;
    }
    } else {
    // Term fits on a new page
    $tempPageRecords = $termRecords;
    $currentPageRowCount = $termRowCount;
    }
    } else {
    // Term fits on current page
    foreach ($termRecords as $record) {
    $tempPageRecords->push($record);
    }
    $currentPageRowCount += $termRowCount;
    }
    }

    // Add the last page if it has any records
    if ($tempPageRecords->count() > 0) {
    $pages[] = $tempPageRecords;
    }

    $totalPages = count($pages);
    @endphp


    @for($pageNum = 1; $pageNum <= $totalPages; $pageNum++)
        @php
        // Get records for current page
        $pageRecords=$pages[$pageNum - 1];
        @endphp

        <div class="container @if($pageNum > 1) page-break-before @endif">
        <!-- HEADER SECTION - Same on all pages -->
        <div class="student-info">
            <div class="left-side">
                <div class="entrance-header">ENTRANCE DATA</div>

                <div class="data-row">
                    <span class="label">Date graduated/last attended</span>
                    <span class="underlined-value">{{ $pageNum == 1 ? ($student->registrationInfos->first()->last_year_attended ?? 'N/A') : '' }}</span>
                </div>

                <div class="data-row">
                    <span class="label">Category</span>
                    <span class="underlined-value" style="width: 260px;">{{ $pageNum == 1 ? ($student->registrationInfos->first()->category ?? 'N/A') : '' }}</span>
                </div>

                <div class="data-row">
                    <span class="label">High School/College</span>
                    @php
                    $lastSchoolAttended = ($student->registrationInfos->first()->last_school_attended ?? 'N/A');
                    $lastSchoolAttended = strlen($lastSchoolAttended);
                    $fontSize = 12; // Default font size

                    if ($lastSchoolAttended > 40) {
                    $fontSize = 7;
                    } elseif ($lastSchoolAttended > 30) {
                    $fontSize = 8;
                    } elseif ($lastSchoolAttended > 25) {
                    $fontSize = 9;
                    }
                    @endphp
                    <span class="underlined-value" style="width: 195px; font-size: {{ $fontSize }}pt;">{{ $pageNum == 1 ? ($student->registrationInfos->first()->last_school_attended ?? 'N/A') : '' }}</span>
                </div>

                <div class="data-row">
                    <span class="label">Date/Semester admitted</span>
                    <span class="underlined-value" style="width: 170px;">
                        @php
                        if ($pageNum == 1) {
                        $acad_term = $student->registrationInfos->first()->academicTerm->acad_term ?? 'N/A';
                        $formatted_term = str_replace('Semester', 'Sem.,', $acad_term);
                        echo $formatted_term;
                        }
                        @endphp
                    </span>
                </div>
            </div>

            <div class="right-side">
                <h4>OFFICIAL TRANSCRIPT OF RECORD</h4>
                <div class="data-row">
                    <span class="label">Name</span>
                    @php
                    $fullName = strtoupper($student->last_name) . ', ' . strtoupper($student->first_name) .
                    (isset($student->suffix) ? ' ' . strtoupper($student->suffix) : '') .
                    (isset($student->middle_name) ? ' ' . strtoupper($student->middle_name) : '');
                    $nameLength = strlen($fullName);
                    $fontSize = 13; // Default font size

                    if ($nameLength > 40) {
                    $fontSize = 9;
                    } elseif ($nameLength > 30) {
                    $fontSize = 10;
                    } elseif ($nameLength > 25) {
                    $fontSize = 12;
                    }
                    @endphp

                    <span class="underlined-value student-name"
                        style="width: 73%; font-style: bold; font-weight: 700; font-size: {{ $fontSize }}pt; font-family: 'Bookman Old Style'; white-space: nowrap;">
                        {{ $fullName }}
                    </span>
                </div>

                <div class="data-row">
                    <span class="label">Address</span>
                    @php
                    $studentAddress = ($student->address ?? 'N/A');
                    $addressLength = strlen($studentAddress);
                    $fontSize = 11; // Default font size

                    if ($addressLength > 40) {
                    $fontSize = 7;
                    } elseif ($addressLength > 30) {
                    $fontSize = 7;
                    } elseif ($addressLength > 25) {
                    $fontSize = 8;
                    }
                    @endphp
                    <span class="underlined-value" style="width: 70%; font-size: {{ $fontSize }}pt;">
                        @if($pageNum == 1)
                        {{ $student->address ?? 'N/A' }}
                        @else
                        @php
                        $numbers = ['one', 'two', 'three', 'four', 'five'];
                        $previousPage = $numbers[$pageNum - 2];
                        @endphp
                        continuation of page {{ $previousPage }}
                        @endif
                    </span>
                </div>

                <div class="data-row">
                    <span class="label">Date of Birth</span>
                    <span class="underlined-value" style="width: 64.9%;">{{ $pageNum == 1 ? ($student->birthdate ? date('F j, Y', strtotime($student->birthdate)) : 'N/A') : '' }}</span>
                </div>

                <div class="data-row">
                    <span class="label">Place of Birth</span>
                    @php
                    $birthPlace = ($student->birthplace ?? 'N/A');
                    $birthLength = strlen($birthPlace);
                    $fontSize = 12; // Default font size

                    if ($birthLength > 40) {
                    $fontSize = 7;
                    } elseif ($birthLength > 30) {
                    $fontSize = 8;
                    } elseif ($birthLength > 25) {
                    $fontSize = 9;
                    }
                    @endphp
                    <span class="underlined-value" style="width: 64%; font-size: {{ $fontSize }}pt;">
                        {{ $pageNum == 1 ? $birthPlace : '' }}
                    </span>
                </div>

                <div class="data-row">
                    <span class="label">Degree/Title/Course</span>
                    <span class="underlined-value" style="width: 55.5%; text-align: center;">
                        @php
                        // Only show on first page
                        if ($pageNum == 1) {
                        // Fetch first record's program and graduation info
                        $program = optional($student->records->first())->program;
                        $latinHonor = optional($student->graduationInfos->first())->latin_honor;

                        // Initialize variables
                        $prefix = '';
                        $remaining = 'N/A';
                        $abbreviation = '';

                        if ($program && $program->program_name) {
                        // Split program name
                        if (str_starts_with($program->program_name, 'Bachelor of Science in')) {
                        $prefix = 'Bachelor of Science in';
                        $remaining = str_replace('Bachelor of Science in ', '', $program->program_name);
                        } elseif (str_starts_with($program->program_name, 'Bachelor of Arts in')) {
                        $prefix = 'Bachelor of Arts in';
                        $remaining = str_replace('Bachelor of Arts in ', '', $program->program_name);
                        }

                        $abbreviation = $program->program_abbreviation ? "({$program->program_abbreviation})" : '';
                        }

                        echo $prefix;
                        }
                        @endphp
                    </span>
                </div>

                <div class="data-row">
                    <span class="label">&nbsp;</span>
                    <span class="underlined-value" style="width: 80%; text-align: center;">
                        {{ $pageNum == 1 ? $remaining : '' }}
                        {{ $pageNum == 1 ? $abbreviation : '' }}
                        @if($pageNum == 1 && isset($latinHonor))
                        , <strong>{{ strtoupper($latinHonor) }}</strong>
                        @endif
                    </span>
                </div>

                <div class="data-row">
                    <span class="label">Major/Specialization</span>
                    <span class="underlined-value">
                        @php
                        if ($pageNum == 1) {
                        $record = $student->records->first();
                        echo $record && $record->programMajor ? $record->programMajor->program_major_name : 'x-x-x-x-x-x';
                        }
                        @endphp
                    </span>
                </div>

                <div class="data-row">
                    <span class="label">Date Conferred</span>
                    <span class="underlined-value" style="width: 61%; align-items: left;">{{ $pageNum == 1 ? ($student->graduationInfos->first()->graduation_date ? date('F j, Y', strtotime($student->graduationInfos->first()->graduation_date)) : 'N/A') : '' }}</span>
                </div>

                <div style="text-align: right;">
                    <span class="underlined-value" style="width: 100%;">
                        @if($pageNum == 1)
                        {{ "per BOR Referendum No. " . ($student->graduationInfos->first()->board_approval ?? 'N/A') }}
                        @else
                        &nbsp;
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <div class="transcript-container">
            <table class="transcript-table">
                <thead>
                    <tr>
                        <th style="width: 15%">Term</th>
                        <th style="width: 15%">Course Code</th>
                        <th style="width: 49%" class="descriptive-title">D E S C R I P T I V E &nbsp; T I T L E</th>
                        <th style="width: 8%">Final<br>Grades</th>
                        <th style="width: 7%">Removal<br>Rating</th>
                        <th style="width: 7%">Units of<br>Credit</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- College/Campus info row ONLY on first page -->
                    @if($pageNum == 1)
                    <tr>
                        <td></td>
                        <td></td>
                        <td class="college-campus-header">
                            @php
                            $record = $student->records()->first();
                            $college = $record ? $record->college->college_name ?? 'N/A' : 'N/A';
                            $campusName = $record ? $record->campus->campus_name ?? 'N/A' : 'N/A';

                            // Set campus name based on college/campus
                            $campus = 'N/A';
                            if ($record && $record->college) {
                            switch ($record->college->college_name) {
                            case 'Bicol University Gubat':
                            $campus = 'Gubat, Sorsogon';
                            break;
                            case 'Bicol University Guinobatan':
                            $campus = 'Guinobatan, Albay';
                            break;
                            case 'Bicol University Polangui':
                            $campus = 'Polangui, Albay';
                            break;
                            case 'Bicol University Tabaco':
                            $campus = 'Tabaco City, Albay';
                            break;
                            default:
                            if ($campusName === 'Main Campus') {
                            $campus = 'Main Campus, Legazpi City';
                            } elseif ($campusName === 'East Campus') {
                            $campus = 'East Campus, Legazpi City';
                            } elseif ($campusName === 'Daraga Campus') {
                            $campus = 'Daraga Campus, Daraga, Albay';
                            } else {
                            $campus = $record->campus->campus_name ?? 'N/A';
                            }
                            }
                            }
                            @endphp
                            {{ strtoupper($college) }}<br>{{ $campus }}
                        </td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif

                    @php
                    $currentTerm = '';
                    $termRowSpanRemaining = 0;
                    $shownTerms = [];

                    // Count how many actual data rows we have (excluding the header row)
                    $actualDataRows = count($pageRecords);

                    // Define different row counts for first page vs. other pages
                    $maxRowsFirstPage = 28; // Fewer rows on first page due to header
                    $maxRowsOtherPages = 30; // More rows on other pages

                    // Use the appropriate row count based on page number
                    $fixedRowsPerPage = ($pageNum == 1) ? $maxRowsFirstPage : $maxRowsOtherPages;

                    // Calculate how many empty rows we need to add
                    $emptyRowsNeeded = max(0, $fixedRowsPerPage - $actualDataRows);
                    @endphp

                    @foreach($pageRecords as $index => $record)
                    @php
                    // Get term from curricula name
                    $term = $record->curricula->curricula_name ?? 'N/A';

                    // Extract only the part before the comma
                    if (strpos($term, ',') !== false) {
                    $term = trim(substr($term, 0, strpos($term, ',')));
                    }

                    // Check if this is a new term
                    $isNewTerm = $currentTerm != $term;

                    // Logic to handle term continuation across pages
                    $showTerm = false;

                    // Only show term if it's new and hasn't been shown before on any page
                    if ($isNewTerm && !in_array($term, $shownTerms)) {
                    $showTerm = true;
                    $shownTerms[] = $term;
                    }

                    if ($isNewTerm) {
                    $currentTerm = $term;

                    // Count how many records for this term are on this page
                    $termRecordsOnThisPage = $pageRecords->filter(function($r) use ($term) {
                    $rTerm = $r->curricula->curricula_name ?? 'N/A';
                    if (strpos($rTerm, ',') !== false) {
                    $rTerm = trim(substr($rTerm, 0, strpos($rTerm, ',')));
                    }
                    return $rTerm == $term;
                    })->count();

                    $termRowSpanRemaining = $termRecordsOnThisPage;
                    }
                    @endphp

                    <tr>
                        <!-- Show term only if needed -->
                        @if($showTerm)
                        <td rowspan="{{ $termRowSpanRemaining }}" class="term-cell">
                            {{ $term }}
                        </td>
                        @endif

                        <td class="course-code">
                            @php
                            $codeParts = explode(' ', $record->course_code);
                            $prefix = $codeParts[0] ?? 'N/A';
                            $number = isset($codeParts[1]) ? $codeParts[1] : '';
                            @endphp
                            <span class="course-code-prefix">{{ $prefix }}</span>
                            <span class="course-code-number">{{ $number }}</span>
                        </td>
                        <td class="desc-title">{{ $record->descriptive_title ?? 'N/A' }}</td>
                        <td style="text-align: center;">{{ is_numeric($record->final_grade) ? number_format($record->final_grade, 1) : $record->final_grade }}</td>
                        <td style="text-align: center;">{{ $record->removal_rating ?? '' }}</td>
                        <td style="text-align: center;">{{ $record->course_unit ?? 'N/A' }}</td>
                    </tr>
                    @endforeach

                    <!-- Add graduation info row if this is the last page -->
                    @if($pageNum == $totalPages)
                    <tr>
                        <td colspan="6" class="graduation-info">
                            <!-- Add minimal spacing before graduation info -->

                            <div style="line-height: 1.2; padding: 0 30px;">
                                GRADUATED WITH THE DEGREE {{ strtoupper($program->program_name ?? '') }}
                                {{ $program && isset($program->program_abbreviation) ? '(' . strtoupper($program->program_abbreviation) . ')' : '' }}
                                {{ isset($latinHonor) ? ', ' . strtoupper($latinHonor) : '' }}
                                ON {{ $student->graduationInfos->first()->graduation_date ? strtoupper(date('F j, Y', strtotime($student->graduationInfos->first()->graduation_date))) : '' }}
                                PER REFERENDUM NO. {{ strtoupper($student->graduationInfos->first()->board_approval ?? '') }}
                                OF THE BOARD OF REGENT, BICOL UNIVERSITY, LEGAZPI CITY
                            </div>

                            @if(isset($student->gwa))
                            <div>
                                <u style="text-decoration: underline; text-decoration-thickness: 1.5;">
                                    GENERAL WEIGHTED AVERAGE = {{ $student->gwa }}
                                </u>
                            </div>
                            @endif

                            <!-- Add consistent spacing after graduation info -->

                        </td>
                    </tr>
                    @endif


                    @php
                    if($pageNum == $totalPages) {
                    $emptyRowsNeeded = max(0, $emptyRowsNeeded - 4);
                    }
                    @endphp


                    @for ($i = 0; $i < $emptyRowsNeeded; $i++)
                        <tr class="empty-row">
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        </tr>
                        @endfor
                </tbody>
            </table>
        </div>

        <!-- Create a new fixed footer section with page-specific class -->
        <div class="page-footer">
            <!-- Photo and grading legend -->
            <div>
                <div class="photo-container">
                    2x2 Photo
                </div>
                <div class="grading-legend">
                    <strong>Official Marks:</strong><br>
                    1.0 -- 99 - 100%; 1.1 -- 98%; 1.2 -- 97%; 1.3 -- 96%; 1.4 -- 95% (Outstanding); 1.5 -- 94%;<br>
                    1.6 -- 93%; 1.7--92% (Superior); 1.8 -- 91%; 1.9 -- 90%; 2.0 -- 89%; 2.1 -- 88%; 2.2 -- 87%;<br>
                    2.3 -- 86%; 2.4 -- 85% (Very Satisfactory); 2.5--84%; 2.6 -- 82-83%; 2.7 -- 80-81% (Satisfactory);<br>
                    2.8 -- 78-79%; 2.9 -- 76-77%; 3.0 -- 75% (Fair); 5.0 -- Failed; INC.--Incomplete.<br><br>
                    <strong>NOTE:</strong><br>
                    This grading system took effect during school year 2001-2002.<br>
                    This copy is an exact reproduction of his/her Official Transcript of Record on file<br>
                    in this office and is considered as original copy when it bears the seal of the University and<br>
                    the original signature in ink of the University Registrar or his duly authorized representative.<br>
                    Any erasure or alteration made on this copy renders the whole transcript invalid.
                </div>


                <div class="remarks" style="margin-top: 0; padding-bottom: 0;">
                    <p>Remarks:
                        <span style="text-decoration: underline; font-weight: bold;">
                            @if($pageNum < $totalPages)
                                @php
                                $numbers=['two' , 'three' , 'four' , 'five' ];
                                $nextPage=$numbers[$pageNum - 1];
                                @endphp
                                continued on page {{ $nextPage }}
                                @else
                                GRANTED FOR HONORABLE DISMISSAL (Not valid for transfer)
                                @endif
                                </span>
                    </p>
                </div>
            </div>

            <footer class="signatures">
                <div class="signature-column">
                    <div style="font-size: 10px;">Prepared by:</div><br>
                    <div style="font-weight: bold; text-align: center; font-size: 12px;">{{ strtoupper($preparedBy->employee_name) }}{{ $preparedBy->suffix ? ', ' . $preparedBy->suffix : '' }}</div>
                    <div style="text-align: center; font-size: 10px;">{{ $preparedBy->employee_designation }}</div><br>
                    <div style="padding-bottom: 5px; font-size: 10px;">Date Issued:</div><br>
                    <div style="border-bottom: 1px solid black; text-align: center; width: 150px ">{{ date('d M Y') }}</div>
                </div>
                <div class="signature-column" style="text-align: left;"><br><br>
                    <div style="font-size: 10px;">Reviewed by:</div><br><br>
                    <div style="font-weight: bold; padding-left: 48px; font-size: 12px;">{{ strtoupper($reviewedBy->employee_name) }}{{ $reviewedBy->suffix ? ', ' . $reviewedBy->suffix : '' }}</div>
                    <div style="padding-left: 70px; font-size: 10px;">{{ $reviewedBy->employee_designation }}</div>
                </div>
                <div class="signature-column" style="text-align: left;"><br><br><br><br>
                    <div style="font-size: 10px;">Certified Correct:</div><br><br>
                    <div style="font-weight: bold; text-align: center; font-size: 14px;">{{ strtoupper($certifiedBy->employee_name) }}{{ $certifiedBy->suffix ? ', ' . $certifiedBy->suffix : '' }}</div>
                    <div style="text-align: center;font-size: 10px;">{{ $certifiedBy->employee_designation }}</div>
                </div>
            </footer>

            <div class="reference-id">A-</div>
            <div class="form-number">
                <span id="effectivity"><strong>BU-F-UREG-14<br>Effectivity Date: April 1, 2016</strong></span>
                <span id="revision"><strong>Revision: 2</strong></span>
            </div>
        </div>
        </div>
        @endfor
</body>

</html>