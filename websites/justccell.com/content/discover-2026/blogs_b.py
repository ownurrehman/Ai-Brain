"""Blogs: leaks, sample trays, medical-grade materials."""

from lib import U, a, bq, h2, h3, hr, ol, p, table, ul


def blog_leak() -> str:
    return "\n".join([
        p(
            "Cheap cartridges leak because the geometry is sloppy, the fill is sloppy, or both. "
            f"Brands pay in oil, returns, dirty 510 batteries, and a reputation that the extract was 'watery'. {a(U['cartridge'], 'Cartridge choice')} is a cost centre, not a line item you win on pennies."
        ),
        p(
            "A leaking cart also trains retail staff to distrust the next SKU from you. "
            "That is slower than a PO cycle and harder to repair than a gasket."
        ),
        bq(
            "The Bottom Line",
            "Leaks follow chimney flood, bad seals, heat, and sideways storage. "
            "Cheap units fail more than one of those at once. "
            "Count leftover oil, dirty pins, and tickets before you celebrate a low cart price.",
        ),
        h2("What a leak costs besides the puddle"),
        p(
            "You lose the oil that left the tank. You lose the unit. You often lose the battery. "
            "You lose a customer who thinks your brand is messy."
        ),
        p(
            "If the cart painted a display tray, you also lose the neighbour SKUs. "
            "Oil creeps. So does the story."
        ),
        h3("Battery collateral"),
        p(
            "Oil on a 510 pin shorts packs and makes charge look broken. "
            f"{a(U['g_charge'], 'Charging guides')} spend a lot of words on this because it is a weekly ticket."
        ),
        p(
            "A 'free' cheap cart that kills a 500 mAh pen is not free."
        ),
        h3("Flavour collateral"),
        p(
            "Oil in the mouthpiece oxidises and tastes like old nuts. "
            "The consumer blames the strain. Your extraction team then 'fixes' a recipe that was fine."
        ),
        hr(),
        h2("The four leak paths we actually see"),
        p(
            "Name the path or you will 'fix' the wrong part."
        ),
        table(
            ["Path", "Usual cause", "First check"],
            [
                ["Mouthpiece / chimney", "Overfill, late cap, warm fill", "Fill mass and cap clock"],
                ["510 shoulder / pin", "Side storage, base gasket, crack", "Orientation and glass"],
                ["Mouthpiece joint", "Bad press, wrong polymer", "Cap process"],
                ["Micro-crack", "Handling, glass, torque", "Incoming lot"],
            ],
        ),
        h3("Chimney floods are process"),
        p(
            f"If the core is ceramic and the fill is brim, believe the fill. "
            "A chimney flood from a brim fill is cheaper to prevent than a new pore size."
        ),
        p(
            "Cheap carts often have wider chimneys or worse mouthpiece seats, so the same overfill looks worse. "
            "That is still a process plus a geometry problem."
        ),
        h3("Base leaks are hardware"),
        p(
            "A whole lot painting pins is a gasket or crack. Stop filling it. "
            "Cheap lots fail incoming inspection more often because nobody paid for inspection."
        ),
        hr(),
        h2("Why the cheap unit fails several paths at once"),
        p(
            "Low price usually means looser tolerances, mystery alloys, and no lot trace. "
            "When oil is thin and terpene-rich, those gaps open together."
        ),
        h3("Tolerances"),
        p(
            "Mouthpiece press-fit that is 'about right' will weep on a warm day. "
            "Premium lines pay for a window. Cheap lines pay later."
        ),
        p(
            "If a supplier cannot show incoming measurements, you are the incoming measurement. "
            "Budget the people."
        ),
        h3("No golden sample"),
        p(
            "Without a kept passed unit, every argument is a vibe. "
            "Cheap programmes skip the cabinet sample because it 'wastes' a cart. It wastes less than a recall."
        ),
        hr(),
        h2("How to price a leak in a pilot"),
        p(
            "Use your numbers, not a blog's."
        ),
        ol([
            "Oil cost per millilitre times average leaked plus leftover.",
            "Returned units at wholesale, not at hope.",
            "Batteries scrapped from pin oil.",
            "Staff hours on tickets.",
            "Retail chargebacks if you have them.",
        ]),
        h3("A worked thought experiment"),
        p(
            "If a cart is 40 cents cheaper and you lose 8 percent to leak and leftover versus 2 percent on a better unit, the oil often eats the 40 cents. "
            "Run it on your oil cost. High-terpene live resin makes the arithmetic rude."
        ),
        p(
            "Finance will still like the 40 cents until you show leftover mass. "
            "Bring the leftover mass."
        ),
        h3("Display damage"),
        p(
            "One leaker in a testers tray can shut a whole brand for a weekend in an independent shop. "
            "That is not on the spreadsheet. It is still real."
        ),
        hr(),
        h2("What to change first: process or part"),
        p(
            "Change process if mouthpiece oil appears after a known late cap. "
            "Change part if process is on the clock and pins still paint."
        ),
        h3("Process first when"),
        ul([
            "Cap delay is unmeasured.",
            "Fills are visual, not weighed.",
            "Trays travel on their side.",
            "Warm fills with no headspace.",
        ]),
        h3("Part first when"),
        p(
            "SOP is tight and two lots of the same SKU differ. "
            "Then you have a supplier lot. Cheap suppliers struggle to tell you which lot."
        ),
        p(
            f"{a(U['n_cer'], 'Ceramic RFQs')} should demand lot codes for this reason."
        ),
        hr(),
        h2("Cheap 510 batteries make leaks look worse"),
        p(
            "A deep, oily well hides oil until it shorts. "
            "A better battery with a cleaner well shows the leak earlier, which is a gift."
        ),
        p(
            f"Do not 'save' money on {a(U['battery'], '510 batteries')} while you debug carts. You will debug both forever."
        ),
        h3("All-in-ones"),
        p(
            "Closed devices leak into USB-C ports and buttons. "
            "That failure is nastier than a replaceable cart. Cheap all-in-ones with weak seals are a brand-killer."
        ),
        p(
            f"{a(U['tank'], 'Tank')} and similar closed SKUs still need the fill SOP. Closed is not sealed-against-stupidity."
        ),
        hr(),
        h2("Stop buying leaky units on unit price"),
        p(
            f"Send oil family, fill mass, and leak history when you {a(U['contact'], 'ask Justccell for a tray')}. "
            "If a cheap cart already failed, tell us the path: mouthpiece or pin."
        ),
        h3("Pilot rules"),
        p(
            "Weigh leftovers. Photograph pins. Do not scale from twelve units."
        ),
        h3("Related"),
        p(
            f"{a(U['g_fill'], 'Fill SOP')} plus a decent ceramic core is the cheap-leak autopsy most teams actually need."
        ),
        h2("Air freight and summer vans"),
        p(
            "Pressure drops and cabin heat turn a marginal seal into a visible leak. "
            "Cheap gaskets move more. If you must ship filled, test the packed SKU, not the naked cart on a desk."
        ),
        p(
            "Empty hardware shipped cool is kinder. "
            "Filled cheap carts shipped as if they were T-shirts is how you fund a competitor's reputation."
        ),
        h3("Pack inserts"),
        p(
            f"Upright {a(U['pack'], 'packaging inserts')} are leak control. "
            "A poly bag is a leak multiplier."
        ),
        p(
            "If retail wants a soft pouch, put a rigid inner. "
            "Fashion without geometry is a puddle."
        ),
        h3("Limitation"),
        p(
            "We cannot price your oil. We can refuse to pretend a 20-cent cart is equivalent to a traced ceramic lot. "
            "Your finance team can still buy the 20-cent cart. They should do it with their eyes open."
        ),
    ])


def extra_blog_leak() -> str:
    return ""


def blog_tray() -> str:
    return "\n".join([
        p(
            "A first sample tray is a test plan in a box, not a gift of colours. "
            f"You are trying to learn whether a core, tank, and cap process survive your oil. {a(U['g_oil'], 'Oil-first hardware selection')} should be boring, labelled, and small."
        ),
        p(
            "Pretty mixed colourways waste oil. "
            "One body, one oil lot, written settings, then you earn the colourway."
        ),
        bq(
            "What You'll Learn",
            "Build a tray around one oil lot, one heater family, and a written voltage. "
            "Label fill mass and cap time. "
            "Pass leak and flavour before you order lasers and cartons.",
        ),
        h2("What belongs on a first tray"),
        p(
            "Empty hardware you will actually fill. The oil lot you will actually sell. "
            "The battery you will actually ship or recommend. Nothing else is required."
        ),
        p(
            "Vision Box style kits exist for structured evaluation. "
            "If you use one, still write your oil on the card. Their demo oil is not your SKU."
        ),
        h3("How many units"),
        p(
            "Enough to fill, cap, hold 24 hours, and still have units to smoke-test. "
            "Twelve is a look. A hundred starts to be a process. Two hundred is a pilot."
        ),
        p(
            "If the supplier only sends twelve, treat it as a look. "
            "Do not forecast annual volume from twelve."
        ),
        h3("What to leave off"),
        p(
            "Five colourways. Three mouthpiece styles. A laser you have not approved. "
            "Those belong on tray two after tray one passes."
        ),
        hr(),
        h2("How to label so the test is repeatable"),
        p(
            "If the card is blank, the test did not happen."
        ),
        ol([
            "Oil lot and family.",
            "Fill mass and temperature.",
            "Cap delay.",
            "Battery SKU and step.",
            "Date and operator initials.",
        ]),
        h3("Photos"),
        p(
            "Photograph pins and mouthpieces at 24 hours. "
            "Memories of 'it was fine' are not evidence."
        ),
        p(
            "Photograph the tray orientation in the fridge or cabinet. "
            "If someone laid it down overnight, the test is void."
        ),
        h3("Blind flavour"),
        p(
            "If you are comparing two cores, hide the names. "
            "People vote for the expensive one when they can see the logo."
        ),
        hr(),
        h2("Pass/fail before anyone talks about brand"),
        p(
            "Fail on mouthpiece oil, pin oil, burnt first hit at the lowest step, or leftover oil that is absurd. "
            "Pass only if the jar and the cart still tell the same story at half tank."
        ),
        table(
            ["Check", "Pass", "Fail"],
            [
                ["24 h upright", "Dry mouthpiece, dry pin", "Wet either"],
                ["First hits", "Jar-like aroma, low volts", "Burnt or harsh"],
                ["End of tank", "Reasonable leftover", "A third still trapped"],
            ],
        ),
        h3("One weeper versus a pattern"),
        p(
            "One weeper can be a missed cap. Three is a process or a lot. "
            "Log both. Do not average them into 'some leaking is normal' unless you have actually decided that number."
        ),
        p(
            "Normal is a rate you chose. It is not a fog."
        ),
        h3("Do not pass on looks"),
        p(
            "A slim body on a lightbox is not a pass. "
            "If marketing needs a hero shot, shoot the passed unit."
        ),
        hr(),
        h2("Tray two is where colour and laser live"),
        p(
            f"Once the core passes, {a(U['n_laser'], 'mark the body')} and freeze the carton. "
            "Doing this on tray one is how you laser a mouthpiece you then reject."
        ),
        h3("CR and pack"),
        p(
            f"If the market needs {a(U['n_cr'], 'child-resistant packs')}, introduce them on tray two or in parallel on empty units. "
            "Do not mix a new pouch foam with a new core in the same 24-hour leak test unless you like confounded results."
        ),
        p(
            "Change one variable per tray when you can."
        ),
        h3("Oil change"),
        p(
            "A new oil lot is a new tray. "
            "Viscosity shifts. The core that passed winter distillate can fail spring live resin."
        ),
        hr(),
        h2("Who should be in the room"),
        p(
            "Operations, not only brand. Someone who fills. Someone who owns tickets. "
            "A photographer is optional. A person with a scale is not."
        ),
        h3("Sales"),
        p(
            "Sales can watch. Sales should not declare a pass because a buyer liked the colour. "
            "Buyers do not see the 24-hour pin."
        ),
        p(
            "If a buyer must be impressed, show the card. Serious buyers like cards."
        ),
        h3("Supplier"),
        p(
            "A supplier who wants to fill with their demo oil is selling a booth. "
            "A supplier who wants your lot number is selling hardware."
        ),
        hr(),
        h2("Request a tray that looks like work"),
        p(
            f"{a(U['contact'], 'Tell Justccell the oil, volume, and whether this is tray one or tray two')}. "
            "Ask for unmarked units if you are still on tray one."
        ),
        h3("Minimum brief"),
        ul([
            "Oil family and lot if you have it.",
            "Fill volume target.",
            "510, pod, or all-in-one.",
            "Battery you will pair, if any.",
        ]),
        h3("Related"),
        p(
            "Oil-first selection is the map. The tray is the field test."
        ),
        h2("What to do with failures"),
        p(
            "Quarantine. Photograph. Do not 'mix them into staff use' so they vanish. "
            "Failed units are data. Staff-use is how data walks out the door."
        ),
        p(
            "If the failure is process, fix the clock and rerun the same hardware. "
            "If the failure is hardware, stop and change SKU. Rerunning a bad core to be polite wastes oil."
        ),
        h3("Telling the supplier"),
        p(
            "Send path (mouthpiece vs pin), fill mass, cap time, and photos. "
            "Do not send 'your carts suck'. They cannot act on that."
        ),
        p(
            "Good suppliers already know this format. "
            "If they argue without asking for the card, believe that too."
        ),
        h3("Limitation"),
        p(
            "A tray does not replace a production capability study. "
            "It stops you from buying 20,000 of a core that dies on day one. That is enough for tray one."
        ),
    ])


def extra_blog_tray() -> str:
    return ""


def blog_med() -> str:
    return "\n".join([
        p(
            "Medical-grade in inhalation hardware is a materials and process claim, not a halo around a lifestyle photo. "
            "If a supplier cannot name polymers, metals, ceramics, and what they were tested against, the phrase is decoration."
        ),
        p(
            "Justccell copy talks about medical-grade inhaler positioning in brand artwork on some assets. "
            f"This article is the buyer version: what to ask so {a(U['safety'], 'safety notes')} are files, not adjectives."
        ),
        bq(
            "In Short",
            "Ask for named materials, food or medical contact standards where they apply, and lot trace. "
            "Medical-grade does not make an oil a medicine. "
            "It does not replace leak tests or licensed-product rules.",
        ),
        h2("What people think medical-grade means"),
        p(
            "They think it means hospital. "
            "In hardware marketing it often means 'we picked a cleaner polymer and a ceramic that is not a hobby clay'."
        ),
        p(
            "Those can be real upgrades. They are still not a marketing authorisation for a drug."
        ),
        h3("What it should mean on an RFQ"),
        p(
            "Named materials (for example a specific ceramic formulation, borosilicate glass, 316L if metal is present). "
            "A statement of oil-contact surfaces. A test panel you actually run (metals, residue, leak)."
        ),
        p(
            "If the supplier says medical-grade and then lists 'premium plastic', keep walking."
        ),
        h3("What it must never mean"),
        p(
            "It must never mean the cart is a licensed inhaler medicine because of the plastic. "
            f"{a(U['n_uk'], 'UK medicines and TRPR')} are separate stacks. Hardware adjectives do not merge them."
        ),
        hr(),
        h2("Oil-contact surfaces worth naming"),
        p(
            "The oil sees the tank, the seals, the heater, the airway, and sometimes a centre post. "
            "Those five are the medical-grade conversation. The outer paint is not."
        ),
        table(
            ["Surface", "Ask", "Red flag"],
            [
                ["Tank", "Glass type or polymer name", "'Plastic' only"],
                ["Heater", "Ceramic lot / film", "'Coil' with no drawing"],
                ["Seals", "Elastomer family, oil swell data", "No swell test on terpenes"],
            ],
        ),
        h3("Elastomers and terpenes"),
        p(
            "Terpene-rich oils swell some rubbers. "
            "A gasket that passed water or nicotine liquid can fail live resin. Demand swell data on a solvent that resembles your oil."
        ),
        p(
            "If they only tested water, they tested water."
        ),
        h3("Glass"),
        p(
            "Borosilicate is named on several Justccell cart cards. "
            "Name it on the PO. 'Glass' includes cheaper soda-lime that chips differently."
        ),
        p(
            "Chips are a safety issue. They are also a leak issue. Incoming visual is part of medical-grade as a practice."
        ),
        hr(),
        h2("Metals testing and why ceramic was the industry move"),
        p(
            "Hardware literature in cannabis has pushed ceramic partly to reduce hot metal in the oil. "
            "That does not make every ceramic cart pass a metals panel. Cheap ceramic with mystery posts still exists."
        ),
        h3("Ask for the panel you will run"),
        p(
            "If your market or your brand standard screens heavy metals in the oil after a standardised session, put that in the RFQ. "
            "Suppliers should know whether they have data."
        ),
        p(
            "No data plus 'medical-grade' is a poem."
        ),
        h3("Certificates"),
        p(
            "Certificates belong to lots. A PDF from 2019 does not cover a 2026 kiln week. "
            "Ask how often they refresh."
        ),
        p(
            f"{a(U['research'], 'R&D capability copy')} is a starting story. Your file still needs dates."
        ),
        hr(),
        h2("Process is part of the grade"),
        p(
            "Clean rooms, incoming inspection, and lot trace are how a materials list stays true. "
            "A perfect polymer processed next to flash and oil mist is not a perfect polymer anymore."
        ),
        h3("Lot trace"),
        p(
            "If a metals spike happens, you need to know which heater lot. "
            "Suppliers who mix lots in a bin cannot help you. That is not medical-grade. That is a tote."
        ),
        p(
            "Print lot codes where the filler can see them without destroying the unit."
        ),
        h3("Filling still contaminates"),
        p(
            "Your needle, your room, your cap. "
            "Medical-grade hardware plus a dirty fill is a dirty product. Own that."
        ),
        hr(),
        h2("Claims on packs and websites"),
        p(
            "Do not print 'medical inhaler' on a lifestyle cart unless your licence and your counsel say that sentence. "
            "Do print materials and fill instructions."
        ),
        h3("Justccell site discipline"),
        p(
            "This Discover hub is for filling partners. "
            "It should not tell Google we copied another brand, and it should not tell patients a cart is a prescription."
        ),
        p(
            "If a translator adds 'hospital quality', strip it. "
            "Adjectives migrate. Files do not."
        ),
        h3("Retail"),
        p(
            "Budtenders will inflate claims. Give them a short honest card: empty hardware, fill spec, voltage. "
            "No miracles."
        ),
        hr(),
        h2("Ask for materials, not poetry"),
        p(
            f"On the {a(U['contact'], 'Justccell brief')}, name the tests you run and the oil-contact questions you need answered. "
            "We will answer with SKUs and drawings, not a halo."
        ),
        h3("RFQ lines that work"),
        ul([
            "Oil-contact materials list.",
            "Heater generation (including 3.0 if required).",
            "Lot coding method.",
            "Which metals or residue tests you will perform.",
        ]),
        h3("Related"),
        p(
            "Ceramic wholesale specs and the safety page are the neighbour documents. "
            "Medical-grade is those pages with less poetry."
        ),
        h2("ISO numbers people drop into Slack"),
        p(
            "ISO 10993 and similar biological-evaluation numbers get dropped as if they were a sticker on a cart. "
            "Those are test programmes with scopes. Ask which parts were tested, in contact with what, on which lot."
        ),
        p(
            "A certificate for a raw polymer pellet is not a certificate for a filled, heated cart. "
            "The process in between counts."
        ),
        h3("Food-contact versus inhalation"),
        p(
            "Food-contact approvals are not automatically inhalation approvals. "
            "Airway plus heat is a different exposure. Do not smuggle a spatula standard into a lung story."
        ),
        p(
            "If your product is a licensed inhaler, your regulatory file already knows this. "
            "If it is not, do not borrow the word inhaler to sound serious."
        ),
        h3("Limitation"),
        p(
            "Justccell is a hardware supplier. We are not your notified body, QP, or toxicologist. "
            "This blog tells you which questions to ask. It does not run the assay."
        ),
    ])


def extra_blog_med() -> str:
    return ""
