<?php
/**
 * Static page copy cloned from ccell.com, Justccell branded.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_static_pages(): array
{
    return [
        'about' => [
            'kicker' => __('About Justccell', 'justccell'),
            'title'  => __('Advancing global wellness through science and technology.', 'justccell'),
            'lede'   => __('We are dedicated to building an innovative, green ecosystem for the atomization space, ensuring our strategic partners and clients succeed, helping our employees realize their dreams, and setting the benchmark for quality lifestyle products.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Corporate culture', 'justccell'),
                    'copy'  => __('Keeping clients and user experience in mind at all times and providing the utmost service and top-notch technology and hardware solutions.', 'justccell'),
                ],
                [
                    'title' => __('Customer centricity', 'justccell'),
                    'copy'  => __('Every device is engineered for the people who fill it and the people who use it. Samples, customization, and production support stay close to that standard.', 'justccell'),
                ],
            ],
            'timeline' => [
                __('Justccell was established as the 3Devices hardware brand.', 'justccell'),
                __('The revolutionary ceramic heating core was launched and redefined the industry standard.', 'justccell'),
                __('TH2 and M6T cartridges were launched, gaining unprecedented popularity.', 'justccell'),
            ],
        ],
        'technology' => [
            'kicker' => __('Why Justccell', 'justccell'),
            'title'  => __('Meet EVOMAX', 'justccell'),
            'lede'   => __('Say goodbye to vape hardware that clogs, leaks, delivers unsatisfying flavors, and works well with distillates but poorly with high-terpene extracts such as live rosins and liquid diamonds.', 'justccell'),
            'image'  => 'public_uploads_images_20240417_1f5e31ac7567518c767e228436d2b848.jpg',
            'blocks' => [
                [
                    'title' => __('All-oil-capable heating', 'justccell'),
                    'copy'  => __('Whether you are working with live rosin, liquid diamonds, live resin, distillate, or another type of oil, the EVOMAX ceramic heating core consistently delivers a clog-free, leak-free, and true-to-strain experience.', 'justccell'),
                ],
                [
                    'title' => __('Isolated airway', 'justccell'),
                    'copy'  => __('Clean vapor and enhanced safety through an isolated airway and clog-free dual air vents across flagship devices.', 'justccell'),
                ],
                [
                    'title' => __('Production-ready hardware', 'justccell'),
                    'copy'  => __('Snap-fit mouthpieces, batch-capping, and filling paths designed so commercial production stays consistent at scale.', 'justccell'),
                ],
            ],
        ],
        'solution' => [
            'kicker' => __('Solution', 'justccell'),
            'title'  => __('Filling & capping solution', 'justccell'),
            'lede'   => __('Say goodbye to tedious days of manual filling and capping one by one. The Justccell filling and capping solution offers quality, efficiency, and affordability that makes filling and capping your devices a simple task.', 'justccell'),
            'image'  => 'public_uploads_images_20250225_08b6cc13898889e8407ea3790ae31cad.png',
            'blocks' => [
                [
                    'title' => __('Quality', 'justccell'),
                    'copy'  => __('Hardware and filling paths that keep oil in the tank and flavor in the vapor, batch after batch.', 'justccell'),
                ],
                [
                    'title' => __('Efficiency', 'justccell'),
                    'copy'  => __('Streamline production and turn filling and capping into a hassle-free process instead of a bottleneck.', 'justccell'),
                ],
                [
                    'title' => __('Affordability', 'justccell'),
                    'copy'  => __('A practical path to commercial filling without sacrificing the device performance your customers expect.', 'justccell'),
                ],
            ],
        ],
        'safety' => [
            'kicker' => __('Safety', 'justccell'),
            'title'  => __('Materials and lock systems built for regulated markets.', 'justccell'),
            'lede'   => __('Child-resistant locks, isolated airways, and food-grade contact parts are designed into flagship devices so brands can meet the safety expectations of the markets they sell into.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Child-resistant options', 'justccell'),
                    'copy'  => __('Selected all-in-ones include child-resistant locks so finished goods stay protected after they leave the filling line.', 'justccell'),
                ],
                [
                    'title' => __('Clean vapor path', 'justccell'),
                    'copy'  => __('Isolated airways keep vapor off heating electronics and reduce the chance of oil contamination.', 'justccell'),
                ],
            ],
        ],
        'research' => [
            'kicker' => __('Research', 'justccell'),
            'title'  => __('Ceramic heating researched for real cannabis oils.', 'justccell'),
            'lede'   => __('Justccell hardware is developed around how live resin, live rosin, liquid diamonds, and distillate actually behave — not a one-viscosity lab oil.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('EVOMAX platform', 'justccell'),
                    'copy'  => __('The EVOMAX heating core is the research output behind clog-free, leak-resistant, true-to-strain performance across cartridges and all-in-ones.', 'justccell'),
                ],
            ],
        ],
        'manufacture' => [
            'kicker' => __('Manufacture', 'justccell'),
            'title'  => __('Built for filling lines, not just photos.', 'justccell'),
            'lede'   => __('Snap-fit mouthpieces, batch-capping, and consistent ceramic cores are specified so contract filling stays repeatable from the first tray to the last.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Batch-capping', 'justccell'),
                    'copy'  => __('Flagship all-in-ones are designed to cap in batches so production teams are not seating mouthpieces one device at a time.', 'justccell'),
                ],
                [
                    'title' => __('Customization', 'justccell'),
                    'copy'  => __('Classic finishes and premium engineering support sit beside the catalog so brands can launch hardware that looks like theirs.', 'justccell'),
                ],
            ],
        ],
        'privacy-policy' => [
            'kicker' => __('Legal', 'justccell'),
            'title'  => __('Privacy policy', 'justccell'),
            'lede'   => __('Justccell collects only what is needed to answer sample and quote requests: name, company, email, country, and the details you send about your extracts and volumes.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('What we use it for', 'justccell'),
                    'copy'  => __('Inquiry details are used to reply, prepare quotes, and ship samples. We do not sell this information.', 'justccell'),
                ],
                [
                    'title' => __('How long we keep it', 'justccell'),
                    'copy'  => __('We keep inquiry records for as long as needed to complete the request and meet accounting or legal duties, then delete or anonymize them.', 'justccell'),
                ],
                [
                    'title' => __('Your requests', 'justccell'),
                    'copy'  => __('To access, correct, or delete personal data from an inquiry, email us through the contact form and include the address you used when you wrote in.', 'justccell'),
                ],
            ],
        ],
    ];
}
