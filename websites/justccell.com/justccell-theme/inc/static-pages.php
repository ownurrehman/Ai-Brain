<?php
/**
 * Static page copy, Justccell branded.
 *
 * Developed by Rank Ray — https://rankray.com
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
    $pages = [
        'about' => [
            'kicker' => __('About Justccell', 'justccell'),
            'title'  => __('About Justccell', 'justccell'),
            'lede'   => '',
            'image'  => 'public_uploads_images_20230509_7e4ef87a737b62c8d0afbd28c9f0be42.jpg',
            'image_mobile' => 'public_uploads_images_20230508_a634bdc9cde9bbc72b1aa4b2f48accc3.jpg',
            'tagline' => __('Hardware without the factory', 'justccell'),
            'sections' => [
                [
                    'id'    => 'corporate-culture',
                    'title' => __('Corporate Culture', 'justccell'),
                    'copy'  => '',
                ],
                [
                    'id'    => 'company-introduction',
                    'title' => __('Company Introduction', 'justccell'),
                    'copy'  => __('Justccell supplies all-in-ones, cartridges, pod systems, and 510 batteries for cannabis extracts. Ceramic cores, isolated airways, and batch-capping paths are specified so commercial filling stays repeatable from the first tray to the last.', 'justccell')
                        . "\n\n"
                        . __('That production backbone — specified cores, QC paths, and filling-line support — is how filling partners launch oil-true hardware without owning a factory.', 'justccell'),
                ],
                [
                    'id'    => 'customer-centricity',
                    'title' => __('Customer Centricity', 'justccell'),
                    'copy'  => '',
                ],
            ],
            'culture' => [
                [
                    'title' => __('Mission', 'justccell'),
                    'copy'  => __('Keep the filler and the consumer in mind at every step: wholesale specs that match production, customization that still seals, and support that stays after the first order.', 'justccell'),
                    'image' => 'public_uploads_images_20211111_06a78ac2c2aafde35cabedee8a4c7525.jpg',
                ],
                [
                    'title' => __('Vision', 'justccell'),
                    'copy'  => __('Build extract hardware that filling partners can launch with confidence — oil-true, leak-resistant devices, without owning a factory.', 'justccell'),
                    'image' => 'public_uploads_images_20211111_7bc553fc856e477b2cbfcc2a8d7894dd.jpg',
                ],
                [
                    'title' => __('Values', 'justccell'),
                    'copy'  => __('Clients and user experience first: finishes, production support, and wholesale guidance that stay close to the standard the device ships at.', 'justccell'),
                    'image' => 'public_uploads_images_20211111_5bd570f19272097fce8f140d16509cec.jpg',
                ],
            ],
            'company_image' => 'public_uploads_images_20230504_244b4a78ffd2fdec80358d8e130e92b7.jpg',
            'customer' => [
                [
                    'title' => __('Customer First', 'justccell'),
                    'copy'  => __('Every device is engineered for the people who fill it and the people who use it. We treat each drop of oil as precious and specify hardware so strain character survives the hit.', 'justccell'),
                    'image' => 'public_uploads_images_20210926_63c7a2324479c893d714f63901b7cdd3.jpg',
                ],
                [
                    'title' => __('We Listen to Your Needs', 'justccell'),
                    'copy'  => __('Finishes and production support stay close to commercial filling. Tell us the oil, the line, and the finish — we route hardware and capping paths around that brief.', 'justccell'),
                    'image' => 'public_uploads_images_20211217_02598608cef6f40dac1fb0682849e145.jpg',
                ],
            ],
            'timeline' => [
                __('Justccell is established as the 3Devices hardware brand.', 'justccell'),
                __('Ceramic heating cores become the default path for thick cannabis oils.', 'justccell'),
                __('TH2 and M6T cartridge families launch for 510 filling lines.', 'justccell'),
                __('Pod systems and compact batteries expand the catalog for on-the-go hardware.', 'justccell'),
                __('EVOMAX and Justccell 3.0 heating platforms extend all-oil and low-temp performance.', 'justccell'),
            ],
            'timeline_years' => [
                [
                    'year'  => '2022',
                    'items' => [
                        __('Justccell is established as the 3Devices hardware brand.', 'justccell'),
                    ],
                ],
                [
                    'year'  => '2023',
                    'items' => [
                        __('Ceramic heating cores become the default path for thick cannabis oils.', 'justccell'),
                    ],
                ],
                [
                    'year'  => '2024',
                    'items' => [
                        __('TH2 and M6T cartridge families launch for 510 filling lines.', 'justccell'),
                    ],
                ],
                [
                    'year'  => '2025',
                    'items' => [
                        __('Pod systems and compact batteries expand the catalog for on-the-go hardware.', 'justccell'),
                    ],
                ],
                [
                    'year'  => '2026',
                    'items' => [
                        __('EVOMAX and Justccell 3.0 heating platforms extend all-oil and low-temp performance.', 'justccell'),
                    ],
                ],
            ],
        ],
        'technology' => [
            'kicker' => __('Why Justccell', 'justccell'),
            'title'  => __('All-New Technology', 'justccell'),
            'lede'   => __('Say goodbye to vape hardware that clogs, leaks, delivers unsatisfying flavors, and works well with distillates but poorly with high-terpene extracts such as live rosins and liquid diamonds.', 'justccell')
                . "\n\n"
                . __('Meet Justccell EVOMAX, the advanced heating platform that works with your oil. Whether you are working with live rosin, liquid diamonds, live resin, distillate, or another type of oil, the EVOMAX ceramic heating core consistently delivers a clog-free, leak-resistant, true-to-strain experience.', 'justccell'),
            'image'        => 'why/justccell-why-technology-hero.jpg',
            'image_mobile' => 'why/justccell-why-technology-hero-mobile.jpg',
            'intro_image'  => 'why/justccell-why-technology-intro.png',
            'meet_heading' => __('Meet EVOMAX', 'justccell'),
            'blocks' => [
                [
                    'title'  => __('Designed for All Cannabis Oils', 'justccell'),
                    'kicker' => __('100% Live Rosin and Liquid Diamond Ready', 'justccell'),
                    'copy'   => __('Due to its optimized internal structure, the EVOMAX heating core keeps supply and vaporization in balance — including oils that fight wick-based hardware, such as live rosins and liquid diamonds.', 'justccell'),
                    'image'  => 'why/justccell-why-technology-row-1.png',
                ],
                [
                    'title' => __('Anti-Clogging & Anti-Leakage', 'justccell'),
                    'copy'  => __('Pore geometry is specified so oil feeds consistently without flooding the airway or starving the first puff, so consumption stays uninterrupted.', 'justccell'),
                    'image' => 'why/justccell-why-technology-row-2.png',
                ],
                [
                    'title' => __('Full Flavor Profiles Unlocked Every Time', 'justccell'),
                    'copy'  => __('Even heat distribution is meant to vaporize terpenes instead of scorching them, so strain character survives the hit.', 'justccell'),
                    'image' => 'why/justccell-why-technology-row-3.png',
                ],
            ],
            'compare' => [
                'left' => [
                    'title' => __('Ceramic heating platform', 'justccell'),
                    'items' => [
                        __('Pure flavor', 'justccell'),
                        __('Consistent performance', 'justccell'),
                        __('Launches at first puff', 'justccell'),
                    ],
                ],
                'right' => [
                    'title' => __('Wick-based technology', 'justccell'),
                    'items' => [
                        __('Burnt taste that contaminates expensive oil', 'justccell'),
                        __('Inconsistent performance', 'justccell'),
                        __('Needs several puffs to launch', 'justccell'),
                    ],
                ],
            ],
        ],
        'solution' => [
            'kicker' => __('Solution', 'justccell'),
            'title'  => __('Filling & capping solution', 'justccell'),
            'lede'   => __('Say goodbye to tedious days of filling and capping one by one. The Justccell filling and capping path is specified for quality, efficiency, and a cost that makes commercial trays realistic.', 'justccell'),
            'image'  => 'public_uploads_images_20250225_08b6cc13898889e8407ea3790ae31cad.png',
            'blocks' => [
                [
                    'title' => __('Quality', 'justccell'),
                    'copy'  => __('Hardware and filling paths that keep oil in the tank and flavor in the vapor, batch after batch.', 'justccell'),
                ],
                [
                    'title' => __('Efficiency', 'justccell'),
                    'copy'  => __('Snap-fit mouthpieces and batch-capping so production is not seating devices one at a time.', 'justccell'),
                ],
                [
                    'title' => __('Affordability', 'justccell'),
                    'copy'  => __('A practical path to commercial filling without giving up the device performance your customers expect.', 'justccell'),
                ],
            ],
            'sections' => [
                [
                    'title' => __('Ask about filling equipment', 'justccell'),
                    'copy'  => __('Compact presses and tray tools are quoted with the hardware so capping 50 units does not need a custom factory line. Include your target units per hour when you inquire.', 'justccell'),
                ],
            ],
        ],
        'safety' => [
            'kicker'       => __('Safety', 'justccell'),
            'title'        => __('Safety', 'justccell'),
            'layout'       => 'split',
            'meet_heading' => __('Stringent Quality Control', 'justccell'),
            'image'        => 'why/justccell-why-safety-hero.jpg',
            'image_mobile' => 'why/justccell-why-safety-hero.jpg',
            'intro_image'  => 'why/justccell-why-safety-intro.jpg',
            'lede'         => __('Based on in-house labs and production checks, incoming materials and finished devices are specified against heavy-metal, leak, and battery-safety tests. Hardware is produced with FDA and RoHS certified raw materials.', 'justccell')
                . "\n\n"
                . __('Justccell continues to invest in research and development to raise the safety floor — child-resistant options, isolated airways, and food-contact or medical-grade 316L stainless paths on flagship devices.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Upgraded Safety', 'justccell'),
                    'copy'  => __('Flagship cartridge cores use medical-grade 316L stainless steel for corrosion resistance in oil contact — the same family of alloy used in food and medical equipment. So at home or on the move, the path oil touches stays specified for contact, not just for photos.', 'justccell'),
                ],
                [
                    'title' => __('Safety is our 1st Priority', 'justccell'),
                    'copy'  => __('Heavy-metal, leak, and function checks sit in the release path before trays leave for filling partners. Child-resistant locks on selected all-in-ones keep finished goods protected after they leave the line.', 'justccell'),
                ],
                [
                    'title' => __('Battery Safety', 'justccell'),
                    'copy'  => __('Cells are specified for capacity, short-circuit, impact, and heat checks, and for lithium transport rules (UN38.3, PI967, SP188) so shipments can move as finished hardware. Devices carry FDA, RoHS, FCC, CE, and UL certificates as applicable.', 'justccell'),
                ],
            ],
        ],
        'research' => [
            'kicker'       => __('R&D Capability', 'justccell'),
            'title'        => __('R&D Capability', 'justccell'),
            'lede'         => __('Since Justccell began, the brief has been ceramic atomization for cannabis oils — live resin, live rosin, liquid diamonds, and distillate as they actually behave, not a one-viscosity lab oil.', 'justccell'),
            'image'        => 'why/justccell-why-research-hero.jpg',
            'image_mobile' => 'why/justccell-why-research-hero.jpg',
            'intro_image'  => 'why/justccell-why-research-intro.png',
            'blocks' => [
                [
                    'title' => __('EVOMAX platform', 'justccell'),
                    'copy'  => __('The EVOMAX heating core is the research output behind clog-resistant, leak-resistant, true-to-strain performance across cartridges and all-in-ones.', 'justccell'),
                ],
                [
                    'title' => __('Just CCELL 3.0', 'justccell'),
                    'copy'  => __('Ultra-low temperature ceramic and film heating for terpene-forward oils that scorch on hotter cores. Ask for the 3.0 brief when you inquire.', 'justccell'),
                ],
                [
                    'title' => __('Oil-type grouping', 'justccell'),
                    'copy'  => __('Catalog groups (distillates, live rosins, live resins, all-oil) exist so fillers pick a platform that matches viscosity instead of hoping one SKU does everything.', 'justccell'),
                ],
            ],
        ],
        'manufacture' => [
            'kicker'       => __('Manufacturing Capability', 'justccell'),
            'title'        => __('Manufacturing Capability', 'justccell'),
            'lede'         => __('Justccell specifies production facilities, safety, hygiene, and testing so filling partners get hardware that is built for the line, not just for photos. Snap-fit mouthpieces, batch-capping, and consistent ceramic cores keep trays repeatable from the first unit to the last.', 'justccell'),
            'image'        => 'why/justccell-why-manufacture-hero.jpg',
            'image_mobile' => 'why/justccell-why-manufacture-hero.jpg',
            'intro_image'  => 'why/justccell-why-manufacture-intro.jpg',
            'meet_heading' => __('Manufacturing Capability', 'justccell'),
            'stats'        => [
                ['value' => '10000', 'unit' => __('Class', 'justccell'), 'label' => __('Cleanliness Grade', 'justccell')],
                ['value' => '5', 'unit' => __('Million', 'justccell'), 'label' => __('Monthly Battery Production', 'justccell')],
                ['value' => '20', 'unit' => __('Million', 'justccell'), 'label' => __('Monthly Atomizer Production', 'justccell')],
                ['value' => '64500', 'unit' => 'm²', 'label' => __('Factory Area', 'justccell')],
            ],
            'blocks' => [
                [
                    'title' => __('Batch-capping', 'justccell'),
                    'copy'  => __('Flagship all-in-ones are designed to cap in batches so production teams are not seating mouthpieces one device at a time.', 'justccell'),
                ],
                [
                    'title' => __('Customization', 'justccell'),
                    'copy'  => __('Classic finishes and premium engineering support sit beside the catalog so brands can launch hardware that looks like theirs.', 'justccell'),
                ],
                [
                    'title' => __('Quality systems', 'justccell'),
                    'copy'  => __('Incoming inspection, in-process checks, and outgoing leak/function tests are part of how trays are released — not a brochure claim after the fact.', 'justccell'),
                ],
            ],
        ],
        'justccell-3-0' => [
            'kicker' => __('Just CCELL 3.0', 'justccell'),
            'title'  => __('Just CCELL 3.0 heating core', 'justccell'),
            'lede'   => __('Ultra-low temperature heating for cannabis oils that lose character on hotter cores. Cottonless ceramic, consistent pores, and a postless path specified for leak and clog resistance.', 'justccell'),
            'image'  => 'public_uploads_images_20250624_586896b2422c482af3eb027b9c112ad5.jpg',
            'blocks' => [
                [
                    'title' => __('True-to-source flavor', 'justccell'),
                    'copy'  => __('A ceramic pore matrix is specified so oil feeds in one direction, reducing reheated oil and off-notes from the first hit to the last.', 'justccell'),
                ],
                [
                    'title' => __('Lower peak temperature', 'justccell'),
                    'copy'  => __('A heating film is specified to even out hot spots and drop peak atomization temperature so terpenes are less likely to scorch.', 'justccell'),
                ],
                [
                    'title' => __('Pure hit, smooth lift', 'justccell'),
                    'copy'  => __('Low-temperature even heating is meant to cut charring and harshness whether the consumer takes a sip or a longer draw.', 'justccell'),
                ],
                [
                    'title' => __('Leak and clog resistance', 'justccell'),
                    'copy'  => __('Interconnected heating channels give oil a predictable path so it is less likely to flood the mouthpiece or starve the core.', 'justccell'),
                ],
            ],
        ],
        'discover' => [
            'kicker' => __('Discover', 'justccell'),
            'title'  => __('Hardware notes for fillers and brand teams.', 'justccell'),
            'lede'   => __('Short Justccell guides — not republished third-party articles. Use these to pick a platform, then contact us for the right hardware line.', 'justccell'),
            'cards'  => [
                [
                    'title' => __('All-new technology', 'justccell'),
                    'copy'  => __('Why ceramic EVOMAX exists, and how it differs from wick-based carts.', 'justccell'),
                    'url'   => '/technology/',
                ],
                [
                    'title' => __('Just CCELL 3.0', 'justccell'),
                    'copy'  => __('Ultra-low temperature heating for terpene-forward oils.', 'justccell'),
                    'url'   => '/justccell-3-0/',
                ],
                [
                    'title' => __('Choose hardware by oil', 'justccell'),
                    'copy'  => __('Distillate, live resin, live rosin, and all-oil devices — which platform to inquire about first.', 'justccell'),
                    'url'   => '/choose-hardware/',
                ],
                [
                    'title' => __('Oil types, plain language', 'justccell'),
                    'copy'  => __('Distillate vs live resin vs live rosin and what that means for viscosity and heat.', 'justccell'),
                    'url'   => '/oil-types/',
                ],
                [
                    'title' => __('What is a 510 thread?', 'justccell'),
                    'copy'  => __('The standard cartridge/battery connection, and which Justccell batteries match it.', 'justccell'),
                    'url'   => '/510-thread/',
                ],
                [
                    'title' => __('Safety', 'justccell'),
                    'copy'  => __('Materials, child-resistant options, and battery transport notes.', 'justccell'),
                    'url'   => '/safety/',
                ],
                [
                    'title' => __('Filling and capping', 'justccell'),
                    'copy'  => __('How batch-capping and snap-fit mouthpieces change the line.', 'justccell'),
                    'url'   => '/solution/',
                ],
                [
                    'title' => __('Manufacturing', 'justccell'),
                    'copy'  => __('What “production-ready” means when you are filling thousands of units.', 'justccell'),
                    'url'   => '/manufacture/',
                ],
            ],
        ],
        'choose-hardware' => [
            'kicker' => __('Discover', 'justccell'),
            'title'  => __('Choose hardware by oil type', 'justccell'),
            'lede'   => __('Start with the extract you actually fill. Speccing the wrong platform wastes oil and time.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Distillates', 'justccell'),
                    'copy'  => __('Thicker, often sweeter oils. Mini Tank, Voca, Flexcell, DS01, Skye II, and Listo are grouped here as a first inquiry set.', 'justccell'),
                ],
                [
                    'title' => __('Live rosins', 'justccell'),
                    'copy'  => __('Solventless, terpene-heavy, easy to clog cheaper cores. Rosin Bar and Vision Box Elite are the starting tray for wholesale quotes.', 'justccell'),
                ],
                [
                    'title' => __('Live resins', 'justccell'),
                    'copy'  => __('Hydrocarbon extracts that still fight generic carts. Flexcell Pro, Voca Pro, Blanc, and Slym are grouped for this viscosity.', 'justccell'),
                ],
                [
                    'title' => __('All-oil-capable', 'justccell'),
                    'copy'  => __('When one SKU must cover more than one oil. Flexcell X, Tank, Eco Star, Vision Box, Voca Pro Max, and Voca Max sit in this group.', 'justccell'),
                ],
            ],
        ],
        'oil-types' => [
            'kicker' => __('Discover', 'justccell'),
            'title'  => __('Distillate, live resin, and live rosin', 'justccell'),
            'lede'   => __('The oil decides the hardware. These notes are for fillers, not medical advice.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Distillate', 'justccell'),
                    'copy'  => __('Highly refined, often thicker, fewer native terpenes unless reintroduced. More forgiving on heat, still leaks if the airway and seals are wrong.', 'justccell'),
                ],
                [
                    'title' => __('Live resin', 'justccell'),
                    'copy'  => __('Frozen-plant hydrocarbon extract. More terpenes and a wider viscosity range. Needs even heat and a core that will not clog mid-cart.', 'justccell'),
                ],
                [
                    'title' => __('Live rosin', 'justccell'),
                    'copy'  => __('Solventless, often the hardest on hardware. Low-temp ceramic and all-oil platforms exist specifically so this oil does not sit in a wick.', 'justccell'),
                ],
            ],
        ],
        '510-thread' => [
            'kicker' => __('Discover', 'justccell'),
            'title'  => __('What is a 510 thread?', 'justccell'),
            'lede'   => __('510 is the common screw connection between a cartridge and a battery. If the cart and battery both say 510, they are meant to mate.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Cartridges', 'justccell'),
                    'copy'  => __('Ceramic-EVOMAX, TH2-EVOMAX, M6T-EVOMAX, TH2-SE, and M6T-SE are 510 carts in the Justccell catalog.', 'justccell'),
                ],
                [
                    'title' => __('Batteries', 'justccell'),
                    'copy'  => __('Stylo, Fino, Sandwave, Go Stik, Palm Pro, M3B Plus, and M3 Plus are 510 batteries. Voltage steps matter as much as thread.', 'justccell'),
                ],
                [
                    'title' => __('Pods and all-in-ones', 'justccell'),
                    'copy'  => __('Pod systems and all-in-ones are closed systems. Do not expect a 510 cart to drop into a Dart or Tank body.', 'justccell'),
                ],
            ],
        ],
        'packaging' => [
            'kicker' => __('Customisation', 'justccell'),
            'title'  => __('Packaging', 'justccell'),
            'lede'   => __('Bespoke sleeves, boxes, and inserts for Justccell hardware. This page is the dedicated packaging brief — owners edit every heading and card in the page fields.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Boxes and sleeves', 'justccell'),
                    'copy'  => __('Retail boxes, inner trays, and device sleeves sized to the SKU. Tell us the unit count, finish, and whether the pack ships filled or empty.', 'justccell'),
                ],
                [
                    'title' => __('Print and finish', 'justccell'),
                    'copy'  => __('Spot colour, foil, and soft-touch options sit on top of the hardware colourway. Artwork is confirmed on a proof before a production run.', 'justccell'),
                ],
                [
                    'title' => __('How to brief us', 'justccell'),
                    'copy'  => __('Add packaging to your wholesale enquiry with quantity, destination, and whether you need child-resistant or plain discrete cartons.', 'justccell'),
                ],
            ],
        ],
        'laser-engraving' => [
            'kicker' => __('Customisation', 'justccell'),
            'title'  => __('Laser engraving', 'justccell'),
            'lede'   => __('From beam to brand — laser engraving is how a device carries your mark. Logos, micro text, and finish sit on the same order as the hardware.', 'justccell'),
            'video'  => 'laser-engraving.mp4',
            'video_heading' => __('Micro text, macro precision', 'justccell'),
            'video_copy' => __('Watch the laser pass. Then add engraving on the product page — colour, logo, and quantity go on the same enquiry as the hardware.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Logos and micro text', 'justccell'),
                    'copy'  => __('Logos, batch marks, and micro text on batteries, pods, and selected all-in-ones. Surface and colourway change the look — we proof a small batch first.', 'justccell'),
                ],
                [
                    'title' => __('Seen with the hardware', 'justccell'),
                    'copy'  => __('The same film plays on every product page, beside the wholesale box, so buyers see engraving with the hardware they are ordering.', 'justccell'),
                ],
                [
                    'title' => __('One enquiry', 'justccell'),
                    'copy'  => __('Contact us from the product page or this page. Include artwork (vector preferred) and whether engraving is per unit or per colourway.', 'justccell'),
                ],
            ],
            'sections' => function_exists('justccell_laser_default_steps') ? justccell_laser_default_steps() : [],
            'cards'    => function_exists('justccell_laser_default_hardware') ? justccell_laser_default_hardware() : [],
            'intro_primary_label'   => __('Contact us', 'justccell'),
            'intro_primary_url'     => '/contact/',
            'intro_secondary_label' => __('Packaging', 'justccell'),
            'intro_secondary_url'   => '/packaging/',
            'steps_heading'         => __('How to brief us', 'justccell'),
            'steps_lede'            => __('Artwork, colourway, and quantity sit on the same enquiry as the hardware. We proof a small batch before a production run.', 'justccell'),
            'hardware_heading'      => __('Hardware we mark', 'justccell'),
            'hardware_lede'         => __('Logos and micro text go on batteries, pods, and selected all-in-ones. Open a product to add engraving to your order.', 'justccell'),
        ],
        'location' => [
            'kicker' => '',
            'title'  => __('Location', 'justccell'),
            'lede'   => '',
            'places' => function_exists('justccell_default_location_rows') ? justccell_default_location_rows() : [],
        ],
        'privacy-policy' => [
            'kicker' => __('Legal', 'justccell'),
            'title'  => __('Privacy policy', 'justccell'),
            'lede'   => __('Justccell (the 3Devices hardware brand) collects only what is needed to answer contact and newsletter requests.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('What we collect', 'justccell'),
                    'copy'  => __('Name, company, email, country, account type (B2B/B2C), VAT number when you are a business, product SKU, and the notes you send about extracts and volumes. Newsletter signups collect email and a privacy consent.', 'justccell'),
                ],
                [
                    'title' => __('What we use it for', 'justccell'),
                    'copy'  => __('To reply, prepare wholesale pricing, and (if you opted in) send product notes. We do not sell this information.', 'justccell'),
                ],
                [
                    'title' => __('Where it is stored', 'justccell'),
                    'copy'  => __('Leads are stored in the WordPress admin (Leads) and emailed to the inquiry inbox. Hosting is on the Justccell WordPress site.', 'justccell'),
                ],
                [
                    'title' => __('How long we keep it', 'justccell'),
                    'copy'  => __('We keep inquiry records as long as needed to complete the request and meet accounting or legal duties, then delete or anonymize them.', 'justccell'),
                ],
                [
                    'title' => __('Your requests', 'justccell'),
                    'copy'  => __('To access, correct, or delete personal data, email the inquiry inbox and include the address you used when you wrote in. EU/UK visitors may also lodge a complaint with their local authority.', 'justccell'),
                ],
                [
                    'title' => __('Cookies', 'justccell'),
                    'copy'  => __('The site uses a store/language cookie so /es/ stays Spain (EUR) when you switch language. See the cookie policy for details.', 'justccell'),
                ],
            ],
        ],
        'terms' => [
            'kicker' => __('Legal', 'justccell'),
            'title'  => __('Terms of use', 'justccell'),
            'lede'   => __('This website presents hardware for licensed cannabis extract businesses. Wholesale quantity tiers on product pages are ex VAT. Card checkout is not open yet — use Add to cart to build your order.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Inquiry, not checkout', 'justccell'),
                    'copy'  => __('Wholesale enquiries are invitations to treat. A contract exists only when 3Devices / Justccell issues written terms you accept.', 'justccell'),
                ],
                [
                    'title' => __('Hardware only', 'justccell'),
                    'copy'  => __('Justccell does not produce, distribute, or sell any material filled in cartridges, pods, or all-in-ones. Fillers are responsible for their oils and finished-goods compliance.', 'justccell'),
                ],
                [
                    'title' => __('Age', 'justccell'),
                    'copy'  => __('Not for sale to minors. Do not use this site if you are under the legal age in your country.', 'justccell'),
                ],
                [
                    'title' => __('Photos', 'justccell'),
                    'copy'  => __('Catalog photography is for design and specification. 3Devices will replace reference images with Justccell-owned assets as they are supplied.', 'justccell'),
                ],
                [
                    'title' => __('Governing law', 'justccell'),
                    'copy'  => __('Until the legal entity and Spanish VAT ID are published in Appearance → Customize, quotes state the contracting party. Update the legal name there when it is confirmed.', 'justccell'),
                ],
            ],
        ],
        'cookies' => [
            'kicker' => __('Legal', 'justccell'),
            'title'  => __('Cookie policy', 'justccell'),
            'lede'   => __('We keep cookies to a minimum: store/country, language, and whatever WordPress needs to log you into wp-admin.', 'justccell'),
            'blocks' => [
                [
                    'title' => __('Store and language', 'justccell'),
                    'copy'  => __('A first-party cookie remembers Spain (/es/) or Switzerland (/ch/). Everyone else stays on justccell.com (UK). Language is ?lang= so Spain can still read English.', 'justccell'),
                ],
                [
                    'title' => __('Coming soon', 'justccell'),
                    'copy'  => __('While the public coming-soon plugin is on, that plugin may set its own cookie for staff bypass. It is not a marketing pixel.', 'justccell'),
                ],
                [
                    'title' => __('Analytics and ads', 'justccell'),
                    'copy'  => __('No marketing pixels are installed in the theme. If 3Devices later adds analytics, this page should be updated before those tags go live.', 'justccell'),
                ],
            ],
        ],
    ];

    return $pages;
}

/**
 * @return list<array{q:string,a:string}>
 */
function justccell_contact_faqs(): array
{
    return [
        [
            'q' => __('Does Justccell fill cartridges?', 'justccell'),
            'a' => __('No. Justccell only produces and sells cartridges. Justccell does not produce, distribute or sell any material filled in cartridges and disposables.', 'justccell'),
        ],
        [
            'q' => __('Why are Justccell cartridges better?', 'justccell'),
            'a' => __('The Justccell cartridge uses a ceramic heating element to replace the traditional cotton core. Evenly distributed coils absorb, store and atomize thick oil so flavor stays pure without scorching or waste.', 'justccell'),
        ],
        [
            'q' => __('What is a 510 thread cartridge?', 'justccell'),
            'a' => __('The most common type of vape cartridge is one with a 510 thread. The threading used to screw the bottom of the cartridge to the appropriate vape battery is referred to as 510.', 'justccell'),
        ],
        [
            'q' => __('Where can I find the product specifications?', 'justccell'),
            'a' => __('Every product page contains specification information. If you are a distributor and seeking additional information, please contact our sales team.', 'justccell'),
        ],
        [
            'q' => __('Where are Justccell products designed and manufactured?', 'justccell'),
            'a' => __('Justccell products are designed and manufactured with strict quality control throughout the entire process, including in-house R&D and production facilities.', 'justccell'),
        ],
        [
            'q' => __('What international manufacturing standards does Justccell comply with?', 'justccell'),
            'a' => __('cGMP certified · ISO 9001 certified · ISO 14001 certified · ISO 13485 certified', 'justccell'),
        ],
        [
            'q' => __('What international safety standards does Justccell adhere to?', 'justccell'),
            'a' => __('Justccell products have obtained safety certificates from authoritative quality systems including FDA, RoHS, FCC, CE, and UL. Battery products comply with UN38.3, PI967 and SP188 transport requirements.', 'justccell'),
        ],
    ];
}

/**
 * Strip CMS-admin wording that leaked onto the public laser page.
 *
 * @param array<string, mixed> $block
 * @return array<string, mixed>
 */
function justccell_laser_public_block(array $block): array
{
    $copy = (string) ($block['copy'] ?? '');
    $hay  = $copy . ' ' . (string) ($block['title'] ?? '');
    if (
        stripos($hay, 'without editing PHP') !== false
        || (stripos($hay, 'Appearance') !== false && stripos($hay, 'Storefront') !== false)
    ) {
        $block['title'] = __('Seen with the hardware', 'justccell');
        $block['copy']  = __('The same film plays on every product page, beside the wholesale box, so buyers see engraving with the hardware they are quoting.', 'justccell');
    }
    return $block;
}

/**
 * @return list<array{id:string,title:string,title_tag:string,copy:string}>
 */
function justccell_laser_default_steps(): array
{
    return [
        [
            'id'        => 'artwork',
            'title'     => __('Send artwork', 'justccell'),
            'title_tag' => 'h3',
            'copy'      => __('Vector logos preferred (AI, EPS, SVG). Tell us whether the mark is per unit or per colourway.', 'justccell'),
        ],
        [
            'id'        => 'proof',
            'title'     => __('Approve a proof', 'justccell'),
            'title_tag' => 'h3',
            'copy'      => __('Surface and colourway change the look. We proof a small batch before a production run.', 'justccell'),
        ],
        [
            'id'        => 'order',
            'title'     => __('Add it to your order', 'justccell'),
            'title_tag' => 'h3',
            'copy'      => __('Colour, logo, and quantity go on the same enquiry as the hardware — from this page or any product page.', 'justccell'),
        ],
    ];
}

/**
 * @return list<array{title:string,title_tag:string,copy:string,url:string,more_label:string}>
 */
function justccell_laser_default_hardware(): array
{
    $url = static function (string $slug): string {
        return function_exists('justccell_category_url')
            ? justccell_category_url($slug)
            : home_url('/' . $slug . '/');
    };
    return [
        [
            'title'      => __('All-In-Ones', 'justccell'),
            'title_tag'  => 'h3',
            'copy'       => __('Selected bodies take a logo or batch mark. Open a SKU and add engraving to your order.', 'justccell'),
            'url'        => $url('all-in-ones'),
            'more_label' => __('View All-In-Ones', 'justccell'),
        ],
        [
            'title'      => __('Cartridges', 'justccell'),
            'title_tag'  => 'h3',
            'copy'       => __('Mouthpiece and hardware marks where the metal allows. Confirm on the product film.', 'justccell'),
            'url'        => $url('cartridge'),
            'more_label' => __('View cartridges', 'justccell'),
        ],
        [
            'title'      => __('Pod systems', 'justccell'),
            'title_tag'  => 'h3',
            'copy'       => __('Battery housings and selected pods. Artwork sits with colourway and quantity.', 'justccell'),
            'url'        => $url('pod-system'),
            'more_label' => __('View pod systems', 'justccell'),
        ],
        [
            'title'      => __('510 batteries', 'justccell'),
            'title_tag'  => 'h3',
            'copy'       => __('The usual place for a brand mark. Micro text and logos both run on these bodies.', 'justccell'),
            'url'        => $url('battery'),
            'more_label' => __('View batteries', 'justccell'),
        ],
    ];
}

function justccell_upgrade_laser_engraving_page(): void
{
    if (get_option('justccell_laser_upgrade_0985') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug') || !function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $page = justccell_find_page_by_slug('laser-engraving');
    if (!$page instanceof WP_Post) {
        return;
    }
    $post_id = (int) $page->ID;

    $lede = get_field('brand_lede', $post_id);
    if (is_string($lede) && (stripos($lede, 'not only a process') !== false || stripos($lede, 'the film on this page') !== false)) {
        update_field(
            'brand_lede',
            __('From beam to brand — laser engraving is how a device carries your mark. Logos, micro text, and finish sit on the same order as the hardware.', 'justccell'),
            $post_id
        );
    }

    $blocks = get_field('brand_blocks', $post_id);
    if (is_array($blocks) && $blocks !== []) {
        $changed = false;
        foreach ($blocks as $i => $row) {
            if (!is_array($row)) {
                continue;
            }
            $clean = justccell_laser_public_block($row);
            if (($clean['title'] ?? '') !== ($row['title'] ?? '') || ($clean['copy'] ?? '') !== ($row['copy'] ?? '')) {
                $blocks[$i]['title'] = $clean['title'];
                $blocks[$i]['copy']  = $clean['copy'];
                $changed = true;
            }
        }
        if ($changed) {
            update_field('brand_blocks', $blocks, $post_id);
        }
    }

    $sections = get_field('brand_sections', $post_id);
    if (!is_array($sections) || $sections === []) {
        update_field('brand_sections', justccell_laser_default_steps(), $post_id);
    }

    $cards = get_field('brand_cards', $post_id);
    if (!is_array($cards) || $cards === []) {
        $seed = [];
        foreach (justccell_laser_default_hardware() as $card) {
            $seed[] = [
                'title'      => $card['title'],
                'title_tag'  => $card['title_tag'],
                'copy'       => $card['copy'],
                'url'        => $card['url'],
                'more_label' => $card['more_label'],
            ];
        }
        update_field('brand_cards', $seed, $post_id);
    }

    update_option('justccell_laser_upgrade_0985', '1', false);
}

function justccell_upgrade_laser_page_editor_fields(): void
{
    if (get_option('justccell_laser_upgrade_0987') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug') || !function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $page = justccell_find_page_by_slug('laser-engraving');
    if (!$page instanceof WP_Post) {
        return;
    }
    $post_id = (int) $page->ID;
    $fallback = justccell_static_pages()['laser-engraving'] ?? [];

    if (function_exists('justccell_laser_page_seed_layout')) {
        justccell_laser_page_seed_layout($post_id, $fallback);
    }

    $cta_title = trim((string) get_field('brand_cta_title', $post_id));
    if (
        $cta_title !== ''
        && (
            stripos($cta_title, 'samples') !== false
            || stripos($cta_title, 'quotes') !== false
        )
    ) {
        update_field('brand_cta_title', '', $post_id);
        update_field('brand_cta_copy', '', $post_id);
        update_field('brand_cta_label', '', $post_id);
        update_field('brand_cta_url', '', $post_id);
    }

    update_option('justccell_laser_upgrade_0987', '1', false);
}

add_action('init', 'justccell_upgrade_laser_engraving_page', 74);
add_action('init', 'justccell_upgrade_laser_page_editor_fields', 75);
