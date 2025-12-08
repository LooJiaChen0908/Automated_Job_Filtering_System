<?php

namespace App\Services;

use Smalot\PdfParser\Parser;
use Carbon\Carbon;

class ResumeParserService
{
    protected $specializations = [
        'software-development' => 'Software Development',
        'web-development' => 'Web Development',
        'mobile-app-development' => 'Mobile App Development',
        'data-science' => 'Data Science',
        'cybersecurity' => 'Cybersecurity',
        'cloud-computing' => 'Cloud Computing',
        'ui-ux-design' => 'UI/UX Design',
        'network-engineering' => 'Network Engineering',
        'qa-testing' => 'QA & Testing',
        'accounting' => 'Accounting',
        'finance' => 'Finance',
        'business-analysis' => 'Business Analysis',
        'marketing' => 'Marketing',
        'sales' => 'Sales',
        'human-resources' => 'Human Resources',
        'graphic-design' => 'Graphic Design',
        'content-writing' => 'Content Writing',
        'social-media' => 'Social Media Marketing',
        'video-production' => 'Video Production',
        'animation' => 'Animation',
        'nursing' => 'Nursing',
        'pharmacy' => 'Pharmacy',
        'biotechnology' => 'Biotechnology',
        'public-health' => 'Public Health',
        'education' => 'Education',
        'logistics' => 'Logistics & Supply Chain',
        'legal' => 'Legal Services',
        'hospitality' => 'Hospitality & Tourism',
        'environmental-science' => 'Environmental Science',
        'translation' => 'Translation & Interpretation',
    ];

    protected $educationKeywords = [
        'spm'       => ['spm', 'o-level', 'ordinary level'],
        'diploma'   => ['diploma', 'advanced diploma'],
        'bachelor'  => ['bachelor', 'b.sc', 'b.a', "bachelor's degree"],
        'master'    => ['master', 'mba', 'msc', 'm.sc', 'm.a', "master's degree"],
        'phd'       => ['phd', 'doctorate', 'doctor of philosophy'],
    ];

    /**
     * Parse resume PDF and return structured data.
     */
    public function parse(string $resumePath): array
    {
        $parser = new Parser();
        $pdf = $parser->parseFile($resumePath);
        $text = strtolower($pdf->getText());
        $text = preg_replace('/\s+/', ' ', $text);

        return [
            'experience_years' => $this->extractExperienceYears($text), // extractExperienceYears extractExperienceDetails
            'specializations'  => $this->detectSpecializations($text),
            'education_level'  => $this->extractEducationLevel($text),
        ];
    }

    /**
     * Extract work experience years from text.
     */

    protected function extractExperienceDetails(string $text): array
    {
        preg_match_all('/(\d{4})\s*[-–]\s*(\d{4})/', $text, $matches, PREG_OFFSET_CAPTURE);

        $experiences = [];

        foreach ($matches[0] as $i => $match) {
            $start = (int)$matches[1][$i][0];
            $end   = (int)$matches[2][$i][0];
            $pos   = $matches[0][$i][1];

            $context = substr($text, max(0, $pos - 200), 400);

            // Skip education ranges
            if (preg_match('/university|college|degree|education|bachelor|master/i', $context)) {
                continue;
            }

            if ($end > $start) {
                $years = $end - $start;

                $experiences[] = [
                    'start' => $start,
                    'end'   => $end,
                    'years' => $years,
                    'context' => $context, // contains job title, company, specialization
                ];
            }
        }

        return $experiences;
    }

    protected function extractExperienceYears(string $text): int
    {
        preg_match_all('/(\d{4})\s*[-–]\s*(\d{4})/', $text, $matches, PREG_OFFSET_CAPTURE);

        $totalExperience = 0;

        foreach ($matches[0] as $i => $match) {
            $start = (int)$matches[1][$i][0];
            $end   = (int)$matches[2][$i][0];
            $pos   = $matches[0][$i][1];

            $context = substr($text, max(0, $pos - 200), 400);

            // Skip education ranges
            if (preg_match('/university|college|degree|education|bachelor|master/i', $context)) {
                continue;
            }

            if ($end > $start) {
                $totalExperience += ($end - $start);
            }
        }

        return $totalExperience;
    }

    /**
     * Detect specialization keywords in resume text.
     */
    protected function detectSpecializations(string $text): array
    {
        // $bestMatch = null;
        // foreach ($this->specializations as $key => $label) {
        //     if (stripos($text, strtolower($label)) !== false) {
        //         if (!$bestMatch || strlen($label) > strlen($bestMatch)) {
        //             $bestMatch = $label;
        //         }
        //     }
        // }
        // return $bestMatch ? [$bestMatch] : [];

        foreach ($this->specializations as $key => $label) {
            if (stripos($text, strtolower($label)) !== false) {
                return [strtolower($label)]; // stop at first match
            }
        }
        return [];
    }

    protected function extractEducationLevel(string $text): ?string
    {
        $text = strtolower($text);
        $detectedLevel = 'none';

        foreach ($this->educationKeywords as $level => $keywords) {
            foreach ($keywords as $keyword) {
                if (strpos($text, $keyword) !== false) {
                    // update if higher level found
                    if ($this->compareEducationLevel($level, $detectedLevel) > 0) {
                        $detectedLevel = $level;
                    }
                }
            }
        }

        return $detectedLevel;

        // if (preg_match('/\b(master|mba|msc|m\.sc|m\.a)\b/i', $text)) {
        //     return 'Master';
        // }
        // if (preg_match('/\b(bachelor|b\.sc|b\.a)\b/i', $text)) {
        //     return 'Bachelor';
        // }
        // if (preg_match('/\b(phd|doctorate|doctor of philosophy)\b/i', $text)) {
        //     return 'PhD';
        // }
        // if (preg_match('/\b(diploma|certificate)\b/i', $text)) {
        //     return 'Diploma/Certificate';
        // }
        // return null;
    }

    protected function compareEducationLevel(string $a, string $b): int
    {
        $order = ['none', 'spm', 'diploma', 'bachelor', 'master', 'phd'];

        $posA = array_search($a, $order);
        $posB = array_search($b, $order);

        return $posA <=> $posB; // returns -1, 0, or 1
    }
}