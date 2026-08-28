"""Five News posts for justccell.com Discover."""

from lib import U, a, bq, h2, h3, hr, ol, p, table, ul


def news_ceramic() -> str:
    return "\n".join([
        p(
            "Wholesale hardware buyers in 2026 should specify the heating core, not only the tank photo. "
            f"Ceramic cores are now the default ask for thick cannabis oils because cotton and exposed metal coils fail those oils in predictable ways. {a(U['tech'], 'Justccell technology notes')} start from that shift."
        ),
        p(
            "The ask is more precise than 'ceramic'. Pore size, whether the centre post is ceramic or metal, and how the heater is filmed all change leak, clog, and flavour. "
            "A buyer who writes 'ceramic cart' on an RFQ still gets a lottery."
        ),
        bq(
            "Key Findings",
            "Specify ceramic geometry, tank material, and oil family on every wholesale RFQ. "
            "Cotton-wick carts remain a mismatch for live resin and rosin. "
            "Core details beat colourways when you score a supplier.",
        ),
        h2("What wholesale buyers mean by ceramic core in 2026"),
        p(
            "A ceramic core is a porous ceramic heater that wicks oil through the ceramic itself rather than through a cotton wrap on a wire. "
            "The ceramic is both the wick and the hot surface."
        ),
        p(
            "That matters because thick oils sit on cotton instead of moving through it. "
            "The cotton then chars. Ceramic pores are sized to move viscous oil at filling-line temperatures without that fibre."
        ),
        h3("Full ceramic versus ceramic-on-metal"),
        p(
            "Full ceramic paths keep oil off a metal centre post. Ceramic-on-metal still uses ceramic at the heater but may run oil past metal. "
            f"{a(U['ceramic'], 'Ceramic-EVOMAX')} is in the catalog as a ceramic-post, glass-body 510 option. Ask which parts are ceramic on the drawing, not only in the headline."
        ),
        p(
            "Metal posts can be fine when the alloy is known and the oil is not aggressively acidic. "
            "Unknown alloys in cheap carts are where heavy-metal scares come from. Require material certificates if your market tests metals."
        ),
        h3("Pore size is a spec"),
        p(
            "Supplier literature often cites pore bands in the 5 to 20 micron neighbourhood. "
            "You do not need to memorise a micron number. You do need the supplier to name one and keep it by lot."
        ),
        p(
            "If pore size drifts, live resin that filled last quarter starts to clog. "
            "That looks like 'the oil changed' when the heater lot changed."
        ),
        hr(),
        h2("Why cotton-wick carts keep showing up in RFQs"),
        p(
            "Cotton is cheap, heats fast, and still works on thin e-liquids. "
            "Some buyers inherit a nicotine-era spec and paste it onto a live-resin programme. That paste job is expensive."
        ),
        p(
            "Cannabis Science and Technology's hardware notes still land where fillers already know: heating elements moved toward ceramic because hot metal plus extract is a flavour and safety problem. "
            "You can argue cost. You cannot argue cotton's behaviour on rosin."
        ),
        h3("Where cotton still appears"),
        p(
            "Budget distillate SKUs and markets that only allow thin oils still see fibre wicks. "
            "If that is your product, say so. Do not buy cotton because the unit price won a spreadsheet against a ceramic cart you never leak-tested."
        ),
        p(
            f"{a(U['b_vs'], 'Ceramic versus cotton')} is the longer comparison. The buying rule is shorter: hard oils get ceramic."
        ),
        h3("Hidden cost of the cheap wick"),
        p(
            "Returns, burnt first hits, and leftover oil that will not wick are core costs. "
            "A 20 percent cheaper cart that strands 30 percent of the oil is not cheaper. It is a write-off with a mouthpiece."
        ),
        ul([
            "Count leftover oil mass on a sample of finished carts.",
            "Count burnt-flavour tickets in the first 14 days.",
            "Count leak returns separately from flavour returns.",
        ]),
        hr(),
        h2("How to write a 2026 hardware RFQ that gets a real quote"),
        p(
            "A useful RFQ names oil family, fill volume, monthly units, tank material, and whether you need 510, pod, or all-in-one. "
            "Colour is a later line. Laser and packaging can be a second stage."
        ),
        h3("Required fields"),
        ol([
            "Oil family and a viscosity note at fill temperature.",
            "Target tank volume and whether you fill in-house.",
            "System: 510 cart, pod, or all-in-one.",
            "Child-resistant need: device, pack, or both.",
            "Testing you will run: metals, leak, drop, CR.",
        ]),
        h3("Optional fields that still help"),
        p(
            "Battery SKU if you ship one. Voltage range if you do not. "
            f"Retail markets you ship to, even at a high level, so {a(U['n_uk'], 'compliance notes')} can be flagged early."
        ),
        p(
            "If you cannot share the oil recipe, still share the family. "
            "Secret sauce is allowed. Secret physics is how you get the wrong pore size."
        ),
        hr(),
        h2("Catalog families that match ceramic-first buying"),
        p(
            "Justccell 510 ceramic carts sit beside all-in-ones that use ceramic heaters in a closed body. "
            "Pick the system from the filling line, then pick the ceramic SKU from the oil."
        ),
        h3("510 ceramic carts"),
        p(
            "Ceramic-EVOMAX, TH2-EVOMAX, and M6T-EVOMAX cover glass and thermoplastic bodies with all-oil-capable claims on the card. "
            "SE versions exist when you need a simpler cart. Do not mix EVOMAX and SE on one SOP without a new leak test."
        ),
        p(
            "Glass is a display choice and a break choice. Thermoplastic is a handling choice. "
            "Neither replaces a core spec."
        ),
        h3("Closed ceramic devices"),
        p(
            f"{a(U['all_in_ones'], 'All-in-ones')} such as Blanc (full ceramic device) and Tank (larger volumes, child-resistant lock on the card) are for brands that want the heater and tank married. "
            "That is the right buy when you do not want a random 510 battery in the wild."
        ),
        p(
            "Blanc's full ceramic path is a different conversation from a glass 510 with a ceramic heater. "
            "Use the words on the drawing."
        ),
        hr(),
        h2("Common specification mistakes in wholesale ceramic buys"),
        p(
            "These mistakes survive because they look professional in a deck."
        ),
        h3("Buying the photo"),
        p(
            "A slim silhouette does not wick rosin. "
            f"Lock the core first, then {a(U['n_laser'], 'engrave and colour')} the body that passed."
        ),
        p(
            "If a sales deck only shows lifestyle photography, ask for a cutaway. "
            "No cutaway, no PO."
        ),
        h3("One core for every oil in a multi-SKU brand"),
        p(
            "A distillate disposable and a live-resin 1 ml cart can share a brand language. "
            "They should not share a heater just to simplify SKU count. Simplifying SKUs is how you complicate returns."
        ),
        table(
            ["Programme", "Core ask", "Avoid"],
            [
                ["Live rosin", "Low-temp ceramic, tight process", "Cotton wick, max voltage"],
                ["Live resin", "Ceramic, low voltage band", "Overfill 'for value'"],
                ["Distillate", "Ceramic or proven wick", "Mystery metal posts"],
            ],
        ),
        hr(),
        h2("What changed versus older wick-era buying"),
        p(
            "Five years ago many RFQs still listed '510 cart' and a millilitre number. "
            "In 2026 the labs test metals, the oils are heavier with terpenes, and retailers return leaky units in public. The RFQ had to grow up."
        ),
        h3("Testing is part of the buy"),
        p(
            "If your market screens heavy metals, put that in the RFQ. "
            "Ceramic reduces some risk paths. It does not make testing optional."
        ),
        p(
            f"{a(U['research'], 'Justccell R&D notes')} exist for filling partners who want the process story, not a slogan."
        ),
        h3("Lead times and lots"),
        p(
            "Ceramic lots are not infinite identical sand. "
            "Ask how lots are traced. If the supplier cannot tell you which kiln week you have, your leak investigation will stall."
        ),
        p(
            "Justccell quotes from a brief. Send oil family and volume. "
            "We will not pretend a colour chip is a core spec."
        ),
        hr(),
        h2("Put ceramic details on the next RFQ"),
        p(
            f"Name the core, the tank, and the oil. Then {a(U['contact'], 'send the Justccell quote brief')} with monthly units. "
            "That is how you get a sample tray instead of a catalogue PDF."
        ),
        h3("Sample acceptance tests"),
        ul([
            "24-hour upright leak on filled samples of your oil.",
            "Flavour at the lowest battery step.",
            "Pin cleanliness after the leak check.",
            "Drop test if retail is unkind.",
        ]),
        h3("Related reading on this hub"),
        p(
            f"Oil-first selection is in {a(U['g_oil'], 'how to choose hardware by oil type')}. "
            "Ceramic buying is the supplier-facing version of the same idea."
        ),
        h2("Centre posts, films, and why drawings beat adjectives"),
        p(
            "A heating film on ceramic is meant to spread power so the peak temperature drops. "
            "That is the Justccell 3.0 story in engineering language. If a supplier only says 'smooth hit', ask whether they film the heater and how they measure peak temperature."
        ),
        p(
            "Centre posts conduct heat and, if metal, can add a taste path. "
            "Postless or ceramic-post designs exist because fillers got tired of explaining a metallic note that was not in the jar."
        ),
        h3("Ask for a cutaway and a lot code"),
        p(
            "Cutaways show whether the airway is isolated. Lot codes show whether you can investigate a spike. "
            "A pretty render with neither is advertising."
        ),
        p(
            "If you buy through a distributor, still demand the lot code on the carton. "
            "Distributors who cannot trace lots are not cheaper. They are fog."
        ),
        h3("What we will not put on an RFQ for you"),
        p(
            "We will not write your legal markets. We will not claim a core makes an unlicensed oil legal. "
            "Empty hardware plus your licence is the model. Keep it that way on paper."
        ),
    ])


def extra_news_ceramic() -> str:
    return "\n".join([
        h2("Scoring suppliers without a theatre demo"),
        p(
            "Demos with a full cart on a booth battery prove a hit, not a process. "
            "Ask for empty samples you fill with your oil. If the supplier refuses, they are selling theatre."
        ),
        p(
            "Score leak, leftover oil, and first-hit flavour on your SOP. "
            "A booth hit on their oil is a different product."
        ),
        h3("Three numbers that belong in the scorecard"),
        ul([
            "Leak rate on your 24-hour upright test.",
            "Average leftover oil mass at 'empty' flavour.",
            "Ticket rate for burnt flavour in the first two weeks of a pilot.",
        ]),
        p(
            "If a supplier cannot wait for those numbers, they are not a wholesale partner. "
            "They are a booth."
        ),
        h3("Pilot size"),
        p(
            "A pilot is hundreds of filled units, not twelve. "
            "Twelve units cannot show a 2 percent leak. Do not scale from twelve."
        ),
        p(
            f"When the pilot passes, freeze the lot rules before you add {a(U['pack'], 'custom packaging')} that cannot change if the body changes."
        ),
    ])


def news_cr() -> str:
    return "\n".join([
        p(
            "Child-resistant hardware is a device problem and a pack problem. "
            "Licensed brands that only shrink-wrap a pretty box still fail markets that test the unit a child can actually open."
        ),
        p(
            "Justccell Tank lists a child-resistant lock on the product card for a reason. "
            f"Packaging inserts and cartons are a second layer. {a(U['pack'], 'Justccell packaging')} is where sleeves and trays get specified after the device passes."
        ),
        bq(
            "The Bottom Line",
            "Treat child-resistance as tested geometry, not a logo. "
            "Device locks, mouthpiece design, and certified packs are different controls. "
            "Rules differ by market: US cannabis often cites ASTM-style pack tests, while UK nicotine vapes sit under TRPR.",
        ),
        h2("What child-resistant means for vape hardware"),
        p(
            "Child-resistant means a child in a defined age band fails to open the product in a timed test, while adults still can. "
            "It is a test result, not a font on a box."
        ),
        p(
            "For hardware, the open action might be a squeeze-and-turn mouthpiece, a lock on an all-in-one, or a pack that must be defeated before the device is even in hand. "
            "You need to know which layer your market scores."
        ),
        h3("Device layer"),
        p(
            "A lock on an all-in-one stops a draw or a mouthpiece removal. "
            "That helps when the device leaves the pack. It does nothing if the lock is optional and retail staff disable it."
        ),
        p(
            "Train the lock as default-on. A CR feature that ships off is a sticker."
        ),
        h3("Pack layer"),
        p(
            "Certified pouches, tins, and cartons are what many cannabis regulators actually test. "
            "A CR device in a gift box that opens like a shoe box can still fail the pack test."
        ),
        p(
            "If you sell in US-style cannabis markets, ask the pack vendor for the protocol they certified against, often an ASTM D3475 family test in those markets. "
            "Do not copy that sentence onto a UK nicotine SKU as if it were universal law."
        ),
        hr(),
        h2("Why UK and US rules are not the same poster"),
        p(
            "UK nicotine e-cigarettes sit under the Tobacco and Related Products Regulations 2016. "
            "Those rules require child-resistant and tamper-evident refill containers and devices in that category, plus tank size limits for nicotine liquids."
        ),
        p(
            "Justccell empty cannabis hardware is not a UK consumer nicotine product by default. "
            "Your finished SKU might be a medicine, a CBPM special, a licensed cannabis good in another country, or something you cannot sell. That is your counsel's job."
        ),
        h3("Do not paste TRPR onto cannabis hardware"),
        p(
            "TRPR tank caps (2 ml) and nicotine strength caps are nicotine-consumer rules. "
            "A 3 ml cannabis all-in-one is a different legal object. Mixing the posters is how brands write illegal claims on a UK site."
        ),
        p(
            f"{a(U['n_uk'], 'UK and Europe hardware notes')} stay high level on purpose. We supply empty devices. You own the finished good."
        ),
        h3("US cannabis pack tests"),
        p(
            "Many US state cannabis rules point at child-resistant packaging tests with child and adult panels. "
            "ASTM D3475 is a name you will hear from pack vendors. It is a US-centric protocol, not a Hostinger checkbox."
        ),
        p(
            "If you ship to those states, budget time for certified packs even when the device has a lock. "
            "Two layers is normal. One layer is a gamble."
        ),
        hr(),
        h2("How to specify CR on a Justccell quote"),
        p(
            "Say whether you need a lock on the device, a certified outer pack, or both. "
            "Say the market. 'Make it CR' with no market is not a spec."
        ),
        h3("Device-side asks"),
        ul([
            "All-in-one with a listed child-resistant lock (Tank is the catalog example).",
            "Mouthpiece removal torque or tool requirement.",
            "Whether the lock must survive a drop test.",
        ]),
        h3("Pack-side asks"),
        p(
            "Outer carton, inner tray, and whether the pack must remain CR after first open (reclosable). "
            "Some markets want reclosable. Some care about first-open only. Do not guess."
        ),
        p(
            "Artwork that shows a child-friendly cartoon character next to a CR claim is how you annoy a reviewer. "
            "Keep the pack adult. Keep the test report in the file."
        ),
        hr(),
        h2("Tamper evidence is not child-resistance"),
        p(
            "A shrink band shows the pack was opened. It does not stop a child. "
            "TRPR even defines tamper-evident as a barrier that shows breach. That is a different job from CR."
        ),
        p(
            "Use both when the market wants both. Do not substitute a hologram sticker for a lock."
        ),
        h3("Where brands mix them up"),
        p(
            "E-commerce photos love intact seals. Warehouse staff then ship returns without a new seal. "
            "Your CR story dies in the returns process. Write a reseal SOP or do not take those returns to resale."
        ),
        p(
            "If a lock can be left off, QC should catch it. "
            "A lock that is hard for adults will generate tickets. Test adults too. CR tests already do. Your staff should."
        ),
        h3("Retail staff behaviour"),
        p(
            "Staff who pre-unlock devices 'to help' are a process hole. "
            "If the lock is part of the legal story, the demo unit still shows the lock on."
        ),
        hr(),
        h2("Hardware examples and what they do not prove"),
        p(
            "Tank's child-resistant lock is a product-card feature for that all-in-one. "
            "It does not certify your finished oil SKU in California or as a UK medicine."
        ),
        p(
            "Pods and 510 carts often rely on the pack for CR because a small cart is easy to mouth. "
            "Plan the pack first for those systems."
        ),
        table(
            ["System", "Typical CR layer", "Watch-out"],
            [
                ["All-in-one", "Device lock plus pack", "Lock shipped off"],
                ["510 cart", "Mostly pack", "Loose carts in a gift box"],
                ["Pod", "Pack plus pod seat", "Easy pod pop-out"],
            ],
        ),
        h3("510 special case"),
        p(
            "A 510 cart on a pen is two objects. Children can meet the cart alone. "
            "If you sell carts separately, the cart pack must do the CR work. The battery box is not enough."
        ),
        p(
            f"Thread education lives in {a(U['g_510'], 'the 510 guide')}. CR education lives on the pack."
        ),
        h3("Honest limitation"),
        p(
            "Justccell is not your notified body. We will not stamp ASTM on a carton from this blog. "
            "We will help you pick a lock geometry and a pack path. You run the test with a lab that owns the protocol."
        ),
        hr(),
        h2("Build CR into the first sample, not the fifth"),
        p(
            "Changing a mouthpiece after artwork is approved is how you slip dates. "
            f"Mention CR on the first {a(U['contact'], 'Justccell contact brief')} with the market name."
        ),
        h3("Files to keep"),
        ol([
            "Device lock description and photos.",
            "Pack test report with protocol name and date.",
            "SOP for lock default-on and reseal on returns.",
        ]),
        h3("Related notes"),
        p(
            f"If the pack also carries brand print, align with {a(U['n_laser'], 'laser and private-label work')} so the CR tab is not covered by foil that fails the test."
        ),
        h2("Adult use versus 'impossible to open'"),
        p(
            "CR tests fail products that adults cannot open either. "
            "A lock that needs a workshop vice will generate returns from the people you can legally sell to."
        ),
        p(
            "Watch older adult users in a hallway test. "
            "If they need a tutorial, simplify the motion before you freeze the tool."
        ),
        h3("One-motion versus two-motion"),
        p(
            "Two-motion opens (squeeze and turn) are common because they beat child panels without needing a hidden tool. "
            "Hidden tools get lost. Then adults use a knife. Then you have damaged locks and a leak."
        ),
        p(
            "If your design needs a tool, tether the tool. "
            "Untethered keys are landfill."
        ),
        h3("Lab shopping"),
        p(
            "Use a lab that already runs the protocol your market names. "
            "A lab that 'does CR' on toys is not automatically qualified for your pouch."
        ),
        p(
            "Send them the finished pack, not a mock-up that uses different board. "
            "Board caliper changes pass/fail."
        ),
    ])


def extra_news_cr() -> str:
    return ""
