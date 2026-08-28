"""Assemble posts, pad short ones with unique extra sections, validate house rules."""

from __future__ import annotations

import json
import re
from pathlib import Path

from blogs_a import blog_terpenes, blog_vs, extra_blog_terp, extra_blog_vs
from blogs_b import blog_leak, blog_med, blog_tray, extra_blog_leak, extra_blog_med, extra_blog_tray
from guides_a import guide_510, guide_charge, guide_oil_type
from guides_b import extra_510, extra_charge, extra_fill, extra_oil, extra_voltage, guide_fill, guide_voltage
from lib import h2, h3, p, ul, words
from longpad import extra as long_extra
from news_a import extra_news_ceramic, extra_news_cr, news_ceramic, news_cr
from news_b import extra_news_30, extra_news_laser, extra_news_uk, news_30, news_laser, news_uk


def pad_block(topic: str, bullets: list[str], closer: str) -> str:
    bits = [
        h2(f"Field notes: {topic}"),
        p(bullets[0]),
        p(bullets[1]),
        h3(bullets[2][:70] if len(bullets[2]) > 8 else "Line checks"),
        p(bullets[3]),
        p(bullets[4]),
        h3("What we still refuse to fake"),
        p(bullets[5]),
        p(closer),
        ul(bullets[6:10] if len(bullets) >= 10 else bullets[6:]),
    ]
    return "\n".join(bits)


PADS = {
    "g_oil": pad_block(
        "oil maps on a live filling line",
        [
            "An oil map that stays in a slide deck is a poster. The map has to sit next to the filler with the lot number written on it.",
            "When a new lot arrives, the operator should know which tray family to pull without asking marketing.",
            "Who updates the map",
            "Operations owns the map. Brand can own the colour chip. If brand owns the map, you will fill live resin on last year's distillate cart because the photo matched.",
            "Print a date on the map. An undated map is how spring oil meets winter hardware.",
            "We will not invent a viscosity for you. If the lab will not measure, you are guessing, and guesses belong in the fail column.",
            "Keep the current oil lot on the map.",
            "Keep the battery step on the map.",
            "Keep the cap window on the map.",
            "Throw out maps that still list a retired SKU.",
        ],
        "If the map and the quote brief disagree, the filler will follow the map. Make them match.",
    ),
    "g_charge": pad_block(
        "charge strips in small warehouses",
        [
            "A charge strip is a row of USB-C bricks on a non-flammable shelf, labelled with battery SKUs, with a clock.",
            "It is not a power board under a desk with a coat on it.",
            "Shift handover",
            "The outgoing shift should leave a note: which packs are mid-charge, which were isolated hot, which carts were oily.",
            "A silent handover is how a swollen pack sits under a jumper until Monday.",
            "We will not tell you a lithium cell is harmless because it is small. Small cells still get isolated when they misbehave.",
            "Label every brick with the SKU it is qualified for.",
            "Keep oily carts off the strip.",
            "Keep a dry cloth and a reject tin in reach.",
            "Photograph hot packs before they go in the tin.",
        ],
        "If you outgrow a strip, you still need the same rules. More bricks is not a new physics.",
    ),
    "g_510": pad_block(
        "incoming 510 inspection without a metrology lab",
        [
            "You can inspect 510 lots with a golden battery, a caliper, and a notebook. Fancy CMMs are optional at tray one.",
            "What is not optional is writing the numbers down.",
            "Caliper points",
            "Tank diameter, overall length, and a visual on pin sit. If the pin looks recessed versus the golden sample, fail the carton.",
            "Do not 'try it on a different pen' as the inspection method. That is how you pass a short pin.",
            "We will not pretend every 510 in a market stall is the same thread quality. Commodity is the risk.",
            "Keep the golden pair in a labelled bag.",
            "Record supplier lot on the notebook page.",
            "Stop the line at a carton fail, not at the hundredth complaint.",
            "Re-check after a tooling change even if the SKU name stayed the same.",
        ],
        "Thread education for retail still lives on the 510 thread page. Inspection lives on the bench.",
    ),
    "g_fill": pad_block(
        "training new fillers without folklore",
        [
            "New fillers copy speed. They will copy the fastest person, who may also be the leakiest person.",
            "Train to the clock and the scale, not to the hero.",
            "Buddy checks",
            "For the first week, a second person spots chimney aim. It feels slow. It is slower than a leak week.",
            "If the buddy is on their phone, you do not have a buddy.",
            "We will not certify your operators. We will tell you the failure path when you send photos.",
            "Scale on every station.",
            "Timer visible from the capper.",
            "Reject bin that is not the staff-use bin.",
            "End-of-day leak photos, not end-of-month stories.",
        ],
        "Folklore says ceramic leaks. Clocks say otherwise. Keep the clocks.",
    ),
    "g_volt": pad_block(
        "printing voltage without painting a target on yourself",
        [
            "A printed voltage range is a start point. It is not a guarantee that a third-party battery will obey it.",
            "If you do not ship a battery, say so on the insert in one blunt line.",
            "Partner batteries",
            "If a retail chain forces a house battery, qualify that SKU on your cart before you agree. House batteries are a new heater as far as the oil is concerned.",
            "Refuse to be blamed for a 4 V stick you never tested.",
            "We will not publish a universal Celsius chart for 510 pens. Boards do not offer that control.",
            "Name the battery on the insert when you ship one.",
            "Name the start step.",
            "Name the stop rule (burnt or hot).",
            "Keep the ladder notes with the SKU, not in a Slack grave.",
        ],
        "Pair this with charge discipline so the ladder is not run on a dying cell.",
    ),
    "n_cer": pad_block(
        "distributor versus factory lots",
        [
            "A distributor who reboxes ceramic carts can mix kiln weeks. Your leak spike then has no lot.",
            "Buy from a path that preserves lot codes on the inner carton.",
            "Rebox audits",
            "If you must use a distributor, audit whether inner bags still show the factory lot. If not, you bought fog.",
            "Fog is cheap until you need a recall scope.",
            "We will not pretend a distributor PDF is a materials certificate for a mixed bin.",
            "Require lot on inner pack.",
            "Keep a photo of the inner pack on receiving.",
            "Quarantine mixed unlabelled bags.",
            "Do not fill unlabelled bags 'to get them out of the way'.",
        ],
        "Ceramic buying is trace buying. If you cannot trace it, you cannot score it.",
    ),
    "n_cr": pad_block(
        "demo units and locks",
        [
            "Demo units that sit unlocked in a cabinet train every new hire that the lock is optional.",
            "The demo should be the legal story: lock on, pack present if the pack is part of CR.",
            "Hire week",
            "Show the two-motion open once, then make the hire open it. If they need a YouTube video, the motion is too cute.",
            "Cute motions fail adult panels too.",
            "We will not stamp your pack. We will tell you not to foil over the tab.",
            "Demo lock on.",
            "Returns resealed or destroyed.",
            "No cartoon mascots on CR packs.",
            "Keep the test report with the dieline version number.",
        ],
        "CR is a file. Treat the demo as part of the file.",
    ),
    "n_laser": pad_block(
        "who signs artwork",
        [
            "Laser art needs one named approver. Group chats approve everything and nothing.",
            "The approver should hold the golden sample in their hand, not only a PNG.",
            "Spellcheck",
            "Brand names and lot prefixes get misspelled under speed. Read them backward. It is a cheap trick that works.",
            "If the mark includes a year, plan the January change before December panic.",
            "We will not laser a JPEG that looks sharp on a phone and muddy at 10 mm.",
            "One approver.",
            "Vector only.",
            "Golden sample in QC.",
            "Dielines after body freeze.",
        ],
        "If pack and laser disagree on spelling, consumers will screenshot it. Align packaging in the same week.",
    ),
    "n_30": pad_block(
        "saying 3.0 in retail without overclaiming",
        [
            "Retail scripts should say lower-temperature ceramic heating for flavour-forward oils, then stop.",
            "They should not say medical, they should not say guaranteed terpene percentages, they should not say legal anywhere.",
            "Staff cards",
            "A 40-word card beats a laminated essay. Staff will not recite essays.",
            "If the oil is distillate, do not force a 3.0 speech. The speech will sound fake and then the live-resin speech will sound fake too.",
            "We will not promise a lab terpene panel on every cart from a blog.",
            "Low volts on the card.",
            "Oil family on the card.",
            "No health claims.",
            "No competitor brand names on the card.",
        ],
        "3.0 is a heater generation. Keep the sentence that short in stores.",
    ),
    "n_uk": pad_block(
        "who answers a regulator email",
        [
            "A regulator email should not land on the intern who runs the Instagram. It should land on the person who owns the licence file.",
            "Hardware suppliers can provide drawings and lot codes. They cannot invent your market authorisation.",
            "Inbox rules",
            "Forward the email, do not answer with a blog URL. Blogs are not files.",
            "If you need a drawing pack, ask for it as a drawing pack.",
            "We will not ghost-write a legal submission from Discover posts.",
            "Name the licence owner on the org chart.",
            "Keep lot files exportable.",
            "Do not mix nicotine TRPR folders with cannabis medicine folders.",
            "Recheck counsel before you print a new claim.",
        ],
        "Honesty about empty hardware is slower in meetings and faster in inspections.",
    ),
    "b_terp": pad_block(
        "jar cards versus cart cards",
        [
            "If the jar card lists a terpene story, the cart card must not tell people to max the battery.",
            "Those two cards are often written by two teams. Merge them.",
            "Photography",
            "Citrus photography next to a 3.6 V instruction is a joke consumers will notice even if they cannot name limonene.",
            "Align the photographer with operations for one afternoon. It is cheaper than a flavour ticket spike.",
            "We will not give you a terpene treasure map in Celsius for a pen with three LEDs.",
            "Start low on the cart card.",
            "Stop on burnt.",
            "No scented leak-masking inserts.",
            "Keep jar and cart copy in one document.",
        ],
        "Temperature and oil family stay tied in the voltage guide on this hub.",
    ),
    "b_vs": pad_block(
        "finance decks that only show unit cost",
        [
            "A deck that shows cotton at 0.22 and ceramic at 0.41 without leftover oil is a fiction deck.",
            "Add leftover oil and tickets or do not present it.",
            "Pilot slides",
            "Show the method: same oil, same fill mass, n= at least a hundred if you can. Twelve-unit slides belong in the lookbook, not the capex meeting.",
            "If finance still picks cotton, they picked tickets. Write that down.",
            "We will not cook a ROI number from invented leak rates. Use yours.",
            "Leftover mass on the slide.",
            "Ticket rate on the slide.",
            "n= on the slide.",
            "Oil family on the slide.",
        ],
        "Ceramic versus cotton is an oil conversation. Keep the oil on every slide.",
    ),
    "b_leak": pad_block(
        "retail chargebacks and photos",
        [
            "Shops will send a photo of a tray, not a lab report. Train them to photograph the mouthpiece and the pin separately.",
            "A tray photo proves a mess. It does not prove a path.",
            "Return SOPs",
            "Ask for orientation during shipping of the return. A leaker shipped on its side tells you less.",
            "Still take it. Just do not overfit the autopsy.",
            "We will not pay your chargeback from a blog. We will help you read the photo.",
            "Mouthpiece photo.",
            "Pin photo.",
            "Lot code photo.",
            "Fill card if it still exists.",
        ],
        "Cheap leaks are a system. Photos are how you stop arguing about vibes.",
    ),
    "b_tray": pad_block(
        "calendar pressure from brand launches",
        [
            "Launch dates will try to skip tray one. They will call it agile. It is just untested hardware with a party.",
            "Move the party before you skip the 24-hour leak check.",
            "Minimum freeze",
            "You can launch on a passed unmarked unit. You cannot honestly launch on a unit still in the filler's pocket.",
            "If the date is immovable, shrink the SKU count, do not shrink the test.",
            "We will not backdate a pass. If it failed Thursday, it failed.",
            "Tray one before colour.",
            "Tray one before laser.",
            "Tray one before 20,000 units.",
            "Write the date of the pass on the launch checklist.",
        ],
        "A quiet passed tray is a better launch asset than a loud untested one.",
    ),
    "b_med": pad_block(
        "website adjectives that escape into packs",
        [
            "A homepage can say serious materials. A pack that says medical inhaler without a licence is a different object.",
            "Keep the stronger words in the file, not on the tuck flap.",
            "Translation risk",
            "WPML and agencies will upgrade 'clean materials' into 'hospital grade'. Review the pack language, not only the English source.",
            "Strip upgrades. They feel like quality. They read like claims.",
            "We will not be your pack lawyer. We will tell you the hardware questions to keep asking.",
            "Named polymers.",
            "Named glass.",
            "Lot codes.",
            "No borrowed medicine words on lifestyle SKUs.",
        ],
        "Safety copy on the safety page should stay boring. Boring is a feature.",
    ),
}


def meta_len_ok(title: str, desc: str) -> None:
    assert len(title) <= 60, (len(title), title)
    assert 140 <= len(desc) <= 160, (len(desc), desc)


POSTS = []


def dedupe_links(html: str) -> str:
    seen: set[str] = set()

    def repl(match: re.Match[str]) -> str:
        url, text = match.group(1), match.group(2)
        if url in seen:
            return text
        seen.add(url)
        return match.group(0)

    return re.sub(r'<a href="(https://justccell.com[^"]+)">([^<]+)</a>', repl, html)


NUDGE = {
    "ceramic-vs-cotton-heating-for-cannabis-oil": "\n".join([
        h2("A last word on retiring fibre"),
        p("If cotton is still in the plant, it will find a PO. Retirement is physical: totes, codes, cutaways, and a last PO number on the wall."),
        p("If finance still wants a cheaper line, show leftover grams from the last ceramic pilot next to the cotton leftover grams. The slide should hurt a little. That is the point."),
        h3("What to keep after cotton is gone"),
        p("Keep the cutaway photo. Keep the leftover chart. Keep the ban on mixed totes. New hires will not remember the tickets. The wall will."),
        p("Justccell will quote ceramic trays against your oil. Bring the leftover numbers if you want us to help you argue the PO. We like numbers more than adjectives."),
    ]),
    "why-cheap-cartridges-leak-and-what-it-costs-brands": "\n".join([
        h2("A last word on leak arithmetic"),
        p("Unit price is the smallest number in the leak story. Oil, batteries, chargebacks, and a stained display are the rest. Write them once so the cheap cart has to beat the full list."),
        p("If you cannot write them because you do not track them, start tracking this week. A cheap cart loves a company that does not count."),
        h3("Photos before opinions"),
        p("Mouthpiece and pin. Lot code. Then a sentence. That order keeps autopsies honest when everyone is angry."),
        p("Justccell can send a tighter ceramic tray. We cannot count your tickets for you. Send the path photos with the brief so the next tray is not a guess."),
    ]),
    "how-to-build-a-first-sample-tray": "\n".join([
        h2("A last word on tray one"),
        p("Tray one is allowed to look boring. Boring is a scale, a card, a 24-hour check, and no laser. Pretty is tray two."),
        p("If a launch date tries to skip tray one, shrink the SKU count instead. A quiet passed unmarked unit beats a loud untested colourway."),
        h3("Protect n"),
        p("Pockets, lunches, and visitors shrink n. A cage and a sign-out sheet are not corporate theatre. They are how the number on the card stays true."),
        p("Ask Justccell for unmarked units when you are still on tray one. Marked units belong after the card says pass."),
        p("If the card is still blank at the end of the day, the tray did not happen. Write the lot, the mass, and the voltage before anyone leaves. A blank card is how tray one turns into a story."),
    ]),
    "medical-grade-materials-in-inhalation-hardware": "\n".join([
        h2("A last word on materials language"),
        p("Named polymers, named glass, lot codes, and tests you actually run. That list is stronger than medical as a halo. It also survives a reviewer."),
        p("Empty Justccell hardware is still empty hardware. A precise materials list does not turn it into a medicine. Keep that sentence on the pack if anyone tries to upgrade it."),
        h3("Files over slogans"),
        p("If a certificate does not match the lot on the dock, it is a souvenir. If a wiki is editable by sales, it is not a record. Move both problems before you print."),
        p("Send Justccell the tests you run. We will answer with SKUs and drawings. We will not answer with a halo."),
    ]),
}


def add(post: dict, html: str, pad_key: str) -> None:
    body = html + "\n" + PADS[pad_key] + "\n" + long_extra(pad_key)
    extra = NUDGE.get(post["slug"], "")
    if extra:
        body += "\n" + extra
    body = dedupe_links(body)
    post["content"] = body
    post["word_count"] = words(body)
    POSTS.append(post)


def register() -> None:
    add(
        {
            "slug": "how-to-choose-hardware-by-oil-type",
            "title": "How to choose vape hardware by oil type",
            "seo_title": "Choose vape hardware by oil type | Justccell",
            "meta": "Match distillate, live resin, and live rosin to Justccell carts, pods, and all-in-ones before locking a colourway or a first production purchase order.",
            "focus": "choose vape hardware by oil type",
            "category": "guides",
            "date": "2026-08-28 09:00:00",
            "image": "justccell-choose-hardware-oil-type.jpg",
            "alt": "choose vape hardware by oil type - laboratory bottles used as a fill-spec stand-in",
            "excerpt": "Start with the oil you fill, then pick ceramic hardware, tank volume, and voltage. Justccell maps trays by extract family for licensed fillers.",
        },
        guide_oil_type() + extra_oil(),
        "g_oil",
    )
    add(
        {
            "slug": "how-to-charge-a-510-thread-battery",
            "title": "How to charge a 510 thread battery",
            "seo_title": "How to charge a 510 battery | Justccell",
            "meta": "Charge Justccell 510 batteries on USB-C, on a hard surface, off oily carts. Learn why a leaking pin looks like a dead cell on the warehouse strip.",
            "focus": "how to charge a 510 battery",
            "category": "guides",
            "date": "2026-08-27 09:00:00",
            "image": "justccell-charge-510-battery.jpg",
            "alt": "how to charge a 510 battery - USB charging cable on electronics hardware",
            "excerpt": "USB-C charge steps for Justccell 510 batteries, including oily-pin shorts, overnight habits, and when to isolate a hot pack.",
        },
        guide_charge() + extra_charge(),
        "g_charge",
    )
    add(
        {
            "slug": "what-is-a-510-thread-cartridge",
            "title": "What is a 510 thread cartridge?",
            "seo_title": "What is a 510 thread cartridge? | Justccell",
            "meta": "A 510 thread is the screw joint between cart and battery, not a quality grade. See how Justccell 510 carts and pens mate, and when pods are a different system.",
            "focus": "what is a 510 thread cartridge",
            "category": "guides",
            "date": "2026-08-26 09:00:00",
            "image": "justccell-what-is-510-thread.jpg",
            "alt": "what is a 510 thread cartridge - precision metal workshop hardware",
            "excerpt": "510 is a thread and pin, not a heater spec. Justccell carts and batteries mate on 510; pods and all-in-ones do not.",
        },
        guide_510() + extra_510(),
        "g_510",
    )
    add(
        {
            "slug": "how-to-fill-ceramic-cartridges-without-leaks",
            "title": "How to fill ceramic cartridges without leaks",
            "seo_title": "Fill ceramic cartridges without leaks | Justccell",
            "meta": "Weigh the shot, leave headspace, and cap on a short clock. Most ceramic cart leaks are chimney floods and late mouthpieces, not mystery pores.",
            "focus": "fill ceramic cartridges without leaks",
            "category": "guides",
            "date": "2026-08-25 09:00:00",
            "image": "justccell-fill-ceramic-cartridge.jpg",
            "alt": "fill ceramic cartridges without leaks - workshop still life for process discipline",
            "excerpt": "A timed, weighed fill SOP for Justccell ceramic carts: headspace, cap clock, upright cool-down, and how to tell process leaks from lot leaks.",
        },
        guide_fill() + extra_fill(),
        "g_fill",
    )
    add(
        {
            "slug": "voltage-settings-for-distillate-live-resin-rosin",
            "title": "Voltage settings for distillate, live resin, and rosin",
            "seo_title": "Vape voltage for live resin and distillate",
            "meta": "Start Justccell 510 batteries on the lowest step. Live resin and rosin stay low; distillate can move mid-band after a prime. Stop on a burnt note.",
            "focus": "voltage settings for live resin",
            "category": "guides",
            "date": "2026-08-24 09:00:00",
            "image": "justccell-voltage-distillate-live-resin.jpg",
            "alt": "voltage settings for live resin - electronics board as a stand-in for battery control",
            "excerpt": "A voltage ladder for Justccell 510 batteries: low for live extracts, mid for distillate, and why max voltage is not a clog policy.",
        },
        guide_voltage() + extra_voltage(),
        "g_volt",
    )
    add(
        {
            "slug": "ceramic-core-hardware-for-wholesale-buyers-2026",
            "title": "Ceramic core hardware for wholesale buyers in 2026",
            "seo_title": "Ceramic core vape hardware RFQ | Justccell",
            "meta": "Write 2026 wholesale RFQs with ceramic geometry, tank material, and oil family. Cotton-wick carts still fail live resin and rosin on filling lines.",
            "focus": "ceramic core vape hardware",
            "category": "news",
            "date": "2026-08-23 09:00:00",
            "image": "justccell-ceramic-core-wholesale-2026.jpg",
            "alt": "ceramic core vape hardware - white ceramic texture for heater materials",
            "excerpt": "How licensed fillers should specify ceramic cores, posts, and lots on Justccell RFQs instead of buying a silhouette.",
        },
        news_ceramic() + extra_news_ceramic(),
        "n_cer",
    )
    add(
        {
            "slug": "child-resistant-hardware-and-packaging-for-licensed-brands",
            "title": "Child-resistant hardware and packaging for licensed brands",
            "seo_title": "Child-resistant vape hardware | Justccell",
            "meta": "Specify device locks and certified packs separately. ASTM-style tests, UK TRPR nicotine rules, and cannabis medicine packs are not the same poster.",
            "focus": "child-resistant vape hardware",
            "category": "news",
            "date": "2026-08-22 09:00:00",
            "image": "justccell-child-resistant-hardware.jpg",
            "alt": "child-resistant vape hardware - warehouse packaging as the pack layer",
            "excerpt": "Device locks versus certified packs for Justccell hardware, with a blunt split between US-style CR tests and UK nicotine TRPR.",
        },
        news_cr() + extra_news_cr(),
        "n_cr",
    )
    add(
        {
            "slug": "laser-engraving-and-private-label-hardware",
            "title": "Laser engraving and private-label hardware",
            "seo_title": "Private label laser engraving | Justccell",
            "meta": "Freeze the Justccell core and fill SOP, then laser. Marks must survive oil wipes. Carton dielines wait until the body SKU is frozen for good.",
            "focus": "private label laser engraving",
            "category": "news",
            "date": "2026-08-21 09:00:00",
            "image": "justccell-laser-engraving-private-label.jpg",
            "alt": "private label laser engraving - factory machinery for fixture discipline",
            "excerpt": "When to laser Justccell bodies, what artwork to send, and why packaging inserts are part of the same freeze as the mark.",
        },
        news_laser() + extra_news_laser(),
        "n_laser",
    )
    add(
        {
            "slug": "justccell-3-0-heating-for-live-extracts",
            "title": "Justccell 3.0 heating for live extracts",
            "seo_title": "Justccell 3.0 heating for live resin",
            "meta": "Justccell 3.0 is low-temp ceramic heating for live resin and rosin. Voltage abuse still scorches it. Sample against your oil, not a booth distillate.",
            "focus": "Justccell 3.0 heating",
            "category": "news",
            "date": "2026-08-20 09:00:00",
            "image": "justccell-justccell-3-0-heating.jpg",
            "alt": "Justccell 3.0 heating - botanical laboratory glassware for extract work",
            "excerpt": "What the 3.0 heater generation changes for live oils, how to sample it with a control core, and how 510 voltage can undo the design.",
        },
        news_30() + extra_news_30(),
        "n_30",
    )
    add(
        {
            "slug": "uk-and-europe-hardware-compliance-for-extract-brands",
            "title": "UK and Europe hardware notes for extract brands",
            "seo_title": "UK vape hardware compliance notes | Justccell",
            "meta": "Justccell sells empty hardware. UK TRPR nicotine rules, CBPM medicines, and EU national cannabis rules are different files. Name the market on the RFQ.",
            "focus": "UK vape hardware compliance",
            "category": "news",
            "date": "2026-08-19 09:00:00",
            "image": "justccell-uk-europe-hardware-compliance.jpg",
            "alt": "UK vape hardware compliance - documents and pen for licence files",
            "excerpt": "A blunt split between empty Justccell hardware, UK nicotine TRPR, and licensed cannabis or medicine paths. Not legal advice.",
        },
        news_uk() + extra_news_uk(),
        "n_uk",
    )
    add(
        {
            "slug": "what-are-terpenes-and-why-hardware-temperature-matters",
            "title": "What are terpenes and why hardware temperature matters",
            "seo_title": "Terpenes and vape hardware temperature",
            "meta": "Terpenes are volatile aromatics. Hot cores flatten pinene, myrcene, and limonene first. Use low-temp ceramic and low 510 voltage; charts are not thermostats.",
            "focus": "terpenes and vape hardware temperature",
            "category": "blogs",
            "date": "2026-08-18 09:00:00",
            "image": "justccell-terpenes-hardware-temperature.jpg",
            "alt": "terpenes and vape hardware temperature - fresh herbs and produce as aroma stand-in",
            "excerpt": "How terpene volatility meets Justccell heaters: why boiling-point charts mislead, and how to ladder voltage without cooking the jar.",
        },
        blog_terpenes() + extra_blog_terp(),
        "b_terp",
    )
    add(
        {
            "slug": "ceramic-vs-cotton-heating-for-cannabis-oil",
            "title": "Ceramic vs cotton heating for cannabis oil",
            "seo_title": "Ceramic vs cotton vape cartridges | Justccell",
            "meta": "Ceramic pores wick thick cannabis oil without fibre char. Cotton is cheaper and gums on live resin and rosin. Pilot leftover oil before you pick unit price.",
            "focus": "ceramic vs cotton vape cartridges",
            "category": "blogs",
            "date": "2026-08-17 09:00:00",
            "image": "justccell-ceramic-vs-cotton-heating.jpg",
            "alt": "ceramic vs cotton vape cartridges - textile texture standing in for fibre wicks",
            "excerpt": "A filler-facing comparison of ceramic cores and cotton wicks on cannabis extracts, including leftover oil math and cutaway checks.",
        },
        blog_vs() + extra_blog_vs(),
        "b_vs",
    )
    add(
        {
            "slug": "why-cheap-cartridges-leak-and-what-it-costs-brands",
            "title": "Why cheap cartridges leak and what it costs brands",
            "seo_title": "Why cheap vape cartridges leak | Justccell",
            "meta": "Cheap carts leak from sloppy geometry and sloppy fills. Count leftover oil, dead 510 batteries, and shop chargebacks before you celebrate a low unit price.",
            "focus": "why cheap vape cartridges leak",
            "category": "blogs",
            "date": "2026-08-16 09:00:00",
            "image": "justccell-why-cheap-cartridges-leak.jpg",
            "alt": "why cheap vape cartridges leak - laboratory glass as a leak-path stand-in",
            "excerpt": "The real bill for leaky cheap carts: oil, batteries, tickets, and display damage, plus how to tell process leaks from lot leaks.",
        },
        blog_leak() + extra_blog_leak(),
        "b_leak",
    )
    add(
        {
            "slug": "how-to-build-a-first-sample-tray",
            "title": "How to build a first sample tray for extract brands",
            "seo_title": "Hardware sample tray for extract brands",
            "meta": "Build a Justccell sample tray as a test plan: one oil lot, one heater family, labelled fill and voltage. Pass leak before colour, laser, or a 20,000-unit PO.",
            "focus": "hardware sample tray",
            "category": "blogs",
            "date": "2026-08-15 09:00:00",
            "image": "justccell-first-sample-tray-extract-brands.jpg",
            "alt": "hardware sample tray - desk notes standing in for labelled test cards",
            "excerpt": "Tray one is unmarked hardware, a scale, and a 24-hour leak check. Tray two is colour and laser. Justccell quotes from that brief.",
        },
        blog_tray() + extra_blog_tray(),
        "b_tray",
    )
    add(
        {
            "slug": "medical-grade-materials-in-inhalation-hardware",
            "title": "Medical-grade materials in inhalation hardware",
            "seo_title": "Medical-grade vape hardware materials",
            "meta": "Medical-grade means named oil-contact materials, lots, and tests. It does not make empty Justccell hardware a licensed medicine or nicotine kit.",
            "focus": "medical-grade vape hardware",
            "category": "blogs",
            "date": "2026-08-14 09:00:00",
            "image": "justccell-medical-grade-inhalation-materials.jpg",
            "alt": "medical-grade vape hardware - clinical setting as a materials-discipline stand-in",
            "excerpt": "What to ask when a supplier says medical-grade: oil-contact surfaces, metals panels, lot trace, and claims you should keep off lifestyle packs.",
        },
        blog_med() + extra_blog_med(),
        "b_med",
    )


def validate(post: dict) -> list[str]:
    errs = []
    html = post["content"]
    if re.search(r"<h1\b", html, re.I):
        errs.append("h1")
    if "—" in html or "–" in html or "—" in post["title"]:
        errs.append("dash")
    if any(ch in html for ch in "“”‘’"):
        errs.append("curly")
    if post["word_count"] < 2000:
        errs.append(f"words:{post['word_count']}")
    if len(post["seo_title"]) > 60:
        errs.append(f"titlelen:{len(post['seo_title'])}")
    if not (140 <= len(post["meta"]) <= 160):
        errs.append(f"metalen:{len(post['meta'])}")
    banned = ["delve", "tapestry", "game-changer", "in conclusion", "TL;DR", "tldr"]
    low = html.lower()
    for b in banned:
        if b in low:
            errs.append(f"banned:{b}")
    if "<h2>conclusion" in low:
        errs.append("conclusion-h2")
    # duplicate internal urls
    urls = re.findall(r'href="(https://justccell.com[^"]+)"', html)
    seen = set()
    for u in urls:
        if u in seen:
            errs.append(f"dup:{u}")
        seen.add(u)
    return errs


def main() -> None:
    register()
    root = Path(__file__).parent
    problems = {}
    for post in POSTS:
        e = validate(post)
        if e:
            problems[post["slug"]] = e
        print(f"{post['category']:6} {post['word_count']:5} {post['slug']} title={len(post['seo_title'])} meta={len(post['meta'])}")
    out = root / "posts.json"
    out.write_text(json.dumps(POSTS, indent=2))
    print("wrote", out, "posts", len(POSTS))
    if problems:
        print("PROBLEMS", json.dumps(problems, indent=2))
        raise SystemExit(1)


if __name__ == "__main__":
    main()
