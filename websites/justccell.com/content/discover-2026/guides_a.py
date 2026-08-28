"""Five Guides posts for justccell.com Discover."""

from lib import BASE, U, a, bq, h2, h3, hr, ol, p, table, ul


def guide_oil_type() -> str:
    return "\n".join([
        p(
            "Start with the oil you actually fill, not the device photo on a sell sheet. "
            f"Distillate, live resin, and live rosin load, heat, and leak differently, which is why {a(U['choose'], 'Justccell hardware maps')} group SKUs by extract family first."
        ),
        p(
            "A thick distillate that sits well in a 1 ml glass cart can flood a live-resin airway. "
            "A solventless rosin that tastes clean on a low-temp ceramic core will char on a cotton wick at the same voltage."
        ),
        bq(
            "What You'll Learn",
            "Match hardware to viscosity and terpene load before you order a production tray. "
            "Use a short sample path: oil type, fill volume, cap timing, then voltage. "
            "Wrong cores waste oil faster than they waste purchase-order time.",
        ),
        h2("What is oil-first hardware selection?"),
        p(
            "Oil-first selection means you specify the extract, fill volume, and filling temperature before you pick a body, mouthpiece, or colourway. "
            "The core, seals, and airway have to survive that oil for the full cart life, not the first three hits."
        ),
        p(
            "Brand teams often reverse this. They pick a slim all-in-one from a moodboard, then ask the lab to make the oil fit. "
            "That is how you get clogged mouthpieces and returns that look like a hardware defect when the mismatch started in purchasing."
        ),
        h3("Viscosity is not a vibe"),
        p(
            "Viscosity is how the oil moves at the temperature you fill and the temperature the consumer stores the device. "
            "Live resin with a high terpene fraction can look thin on the bench and still gum a small chimney overnight."
        ),
        p(
            "Distillate cut with a modest botanical terpene blend is usually more forgiving. "
            "Rosin and high-terpene live resin are not. Treat them as different SKUs even if both sit in a 0.5 ml tank."
        ),
        h3("Terpene load changes heat and leak risk"),
        p(
            "Monoterpenes such as pinene and myrcene leave the oil earlier than the cannabinoid fraction. "
            f"If the core runs hot, you lose the top notes and you also thin the remaining oil, which is a leak path. {a(U['b_terp'], 'Hardware temperature and terpenes')} belong in the same brief."
        ),
        p(
            "Ask your lab for a viscosity reading at fill temperature and at 20 C. "
            "One number at room temperature is not enough for a live extract that you warm to 50 C on the filler."
        ),
        hr(),
        h2("Why oil type decides the platform"),
        p(
            "Closed all-in-ones, 510 cartridges, and pod systems fail in different ways on the same oil. "
            "The platform is a leak geometry plus a heat geometry. You pick both, not a silhouette."
        ),
        p(
            "A 510 cart lets you swap batteries and voltage later. "
            "An all-in-one locks the heater and the tank together, which is cleaner for retail but less forgiving if the first voltage guess is wrong."
        ),
        h3("All-in-ones versus 510 carts"),
        p(
            f"{a(U['all_in_ones'], 'Justccell all-in-ones')} such as Tank, Eco Star, and Mini Tank are built for filling lines that want a finished device, not a separate battery SKU. "
            "That helps when the brand owns the full consumer unit."
        ),
        p(
            f"{a(U['cartridge'], '510 cartridges')} stay useful when your retail partners already stock variable-voltage batteries. "
            "They also let you trial cores without committing the whole device body."
        ),
        h3("Pods are not a 510 with a clip"),
        p(
            f"Pod systems such as {a(U['luster'], 'Luster Pro')} and Dart keep oil and heater in a closed cartridge that only mates with its battery. "
            "Do not plan to drop a 510 cart into a Dart body. The thread, contacts, and tank are a different system."
        ),
        p(
            "Pods help when you want temperature steps without exposing a 510 pin. "
            "They hurt when you already have a warehouse of 510 batteries you intended to reuse."
        ),
        hr(),
        h2("How distillate, live resin, and live rosin differ in hardware"),
        p(
            "Distillate is the most refined of the three common filling oils. "
            "Live resin keeps more native terpenes from frozen plant. Live rosin is solventless and usually the hardest on cheap cores."
        ),
        p(
            f"Justccell groups first trays on the {a(U['oil'], 'oil types page')} because those three oils do not share a single safe core. "
            "If your SKU is a blend, name the dominant fraction. Mixed-oil marketing copy is not a spec."
        ),
        h3("Distillate trays"),
        p(
            "Distillate often wants a slightly higher heat band to move. "
            "It still leaks if you overfill or delay the cap. Heat does not excuse a missing headspace."
        ),
        p(
            "First sample set on the Justccell map includes Mini Tank, Voca, Flexcell, DS01, Skye II, and Listo. "
            "Start there before you special-order a colourway."
        ),
        h3("Live resin and live rosin trays"),
        p(
            "Live resin is grouped around Flexcell Pro, Voca Pro, Blanc, and Slym. "
            f"Live rosin starts at {a(U['rosin_bar'], 'Rosin Bar')} and Vision Box Elite because those platforms are specified for solventless oils that clog wick cores."
        ),
        p(
            "If you fill both live resin and distillate on one line, do not assume one cart body covers both. "
            "Run two sample SKUs. The extra tray costs less than a mixed-oil return spike."
        ),
        hr(),
        h2("How to run a first sample tray"),
        p(
            "A useful sample tray is small, labelled, and tied to one oil lot. "
            "You are not tasting devices. You are watching fill, cap, 24-hour leak, and flavour at a named voltage."
        ),
        p(
            f"Write the oil lot, fill temperature, and cap delay on the tray card. "
            f"{a(U['b_tray'], 'Building that first tray')} is a process, not a gift box of colours."
        ),
        h3("What to put on the card"),
        ol([
            "Oil type and lot number, plus viscosity at fill temperature.",
            "Target fill volume (0.3, 0.5, 1.0, 1.2, 2.0, or 3.0 ml) and actual grams in.",
            "Cap time from last drop to seated mouthpiece.",
            "Storage orientation for 24 hours (upright only unless you are testing abuse).",
            "Battery model and voltage for the smoke test.",
        ]),
        h3("Pass and fail signals"),
        p(
            "Fail the SKU if oil sits in the mouthpiece after 24 hours upright, if the first draw is burnt at the lowest voltage, or if more than a film of oil coats the 510 pin. "
            "A single weeping cart can be a cap error. Three in a row is the hardware."
        ),
        p(
            "Pass means the airway stays clear through half the tank and the flavour still matches the jar. "
            "Do not pass a cart because it looks premium on a lightbox."
        ),
        hr(),
        h2("Common mistakes when picking hardware by oil"),
        p(
            "Most expensive mistakes happen before the first production PO. "
            "They look like design choices. They are specification gaps."
        ),
        h3("Ordering colour before core"),
        p(
            "Colourways and laser marks do not change pore size. "
            f"Finish work belongs after the core passes. {a(U['n_laser'], 'Private-label engraving')} is a second conversation."
        ),
        p(
            "If marketing needs a mock-up, mock the passed SKU. "
            "Do not lock packaging dielines to a body that failed leak."
        ),
        h3("One voltage for every oil"),
        p(
            "A 3.2 V setting that clears thick distillate will flatten live resin. "
            f"Put voltage on the sell sheet next to the oil name. {a(U['g_volt'], 'Voltage by extract type')} is part of hardware choice, not an accessory note."
        ),
        ul([
            "Live rosin and high-terpene live resin: start at the lowest battery step.",
            "Standard distillate: mid band after a low-voltage prime.",
            "Very thick distillate: only step up if vapour is weak, not because the LED looks 'sport'.",
        ]),
        hr(),
        h2("Oil-first selection versus photo-first selection"),
        p(
            "Photo-first selection starts with a silhouette. Oil-first selection starts with a lab sheet. "
            "The second method produces fewer returns even when the device looks less fashionable in the first round."
        ),
        table(
            ["Approach", "You decide first", "Typical failure"],
            [
                ["Photo-first", "Body and colour", "Clog, scorch, leak on live oils"],
                ["Oil-first", "Extract and fill spec", "Longer sample cycle, fewer recalls"],
                ["Hybrid", "Oil plus one body family", "Works if you freeze the core"],
            ],
        ),
        h3("When a hybrid still works"),
        p(
            "A hybrid is fine when the brand already standardised on 510 retail batteries. "
            "You still pick the cart family by oil, then you pick the battery from the 510 rack."
        ),
        p(
            f"{a(U['battery'], 'Justccell 510 batteries')} with stepped voltage exist so the cart can stay the same while the oil programme changes. "
            "That only helps if the cart was chosen for the new oil, not inherited from last year's distillate run."
        ),
        h3("What we will not pretend"),
        p(
            "No catalog line, including ours, makes every oil legal in every country. "
            "Justccell sells empty hardware to licensed fillers. Your licence, your oil, your market rules sit on your side of the quote."
        ),
        p(
            f"If you need a geography check, start with {a(U['n_uk'], 'UK and Europe hardware notes')} and your counsel. "
            "Hardware pages are not a legal opinion."
        ),
        hr(),
        h2("Request a tray that matches the oil you fill"),
        p(
            "Send the oil type, fill volume, and whether you need child-resistant or laser-ready parts. "
            f"Use the {a(U['contact'], 'Justccell contact form')} rather than a consumer-style basket. Quotes are built from that brief."
        ),
        h3("What speeds up a quote"),
        ul([
            "Oil family: distillate, live resin, live rosin, or a named blend.",
            "Monthly volume in units, even if it is a range.",
            "Fill method: hand, benchtop, or automated line.",
            "Whether packaging and engraving are in scope on the first PO.",
        ]),
        h3("What slows it down"),
        p(
            "Moodboards with no oil spec. Requests for 'the same as the viral all-in-one' with no viscosity. "
            "We can still help, but we will ask for the lab sheet before we lock a SKU."
        ),
        p(
            "If you already ran a failed cart, send the failure mode: leak, clog, burnt first hit, or pin oil. "
            "That is more useful than a competitor part number."
        ),
    ])


def guide_charge() -> str:
    return "\n".join([
        p(
            "Most Justccell 510 batteries charge over USB-C. "
            f"Seat the cable fully, keep the port dry, and stop at a full indicator rather than leaving the pack on a charger overnight. {a(U['battery'], '510 battery pages')} list capacity and charge notes per SKU."
        ),
        p(
            "A pack that will not take charge is often a shorted 510 pin from a leaking cart, not a dead cell. "
            "Check the cartridge contact before you scrap the battery."
        ),
        bq(
            "What You'll Learn",
            "Charge 510 batteries with a known USB-C source, on a hard surface, off the cart when you can. "
            "Do not leave packs on a charger overnight as a habit. "
            "A leaking cartridge can look like a battery failure.",
        ),
        h2("What is a 510 battery charge cycle?"),
        p(
            "A charge cycle is the path from a depleted lithium-ion cell back to the voltage the board treats as full. "
            "USB-C is the connector. The charge controller inside the battery decides current and cutoff."
        ),
        p(
            "Consumer USB-C chargers vary. A laptop port, a phone brick, and a cheap multi-head charger do not deliver the same behaviour. "
            "Use one known brick for warehouse tests so you are not debugging the wall wart."
        ),
        h3("USB-C is not a chemistry"),
        p(
            "USB-C only describes the port. It does not mean the pack supports PD handshake the way a laptop does. "
            "Many 510 batteries want a simple 5 V source. A high-watt laptop charger that negotiates PD can confuse a small board."
        ),
        p(
            "If a pack gets hot on a laptop dock and stays cool on a 5 V phone brick, keep the brick. "
            "Heat during charge is a stop condition, not a 'fast charge' feature on these cells."
        ),
        h3("Why overnight charging is a bad warehouse habit"),
        p(
            "A proper charge controller should cut off. Cheap boards and tired cells still sit at float. "
            "Overnight charging in a sealed tote also hides a swollen pack until the morning shift."
        ),
        p(
            "Set a timer. Move full packs off the cable. Store them at partial charge if they will sit for weeks. "
            "Full-to-empty cycling every night is not required for lithium-ion 510 packs."
        ),
        hr(),
        h2("How to charge a Justccell 510 battery step by step"),
        p(
            "The safe sequence is: inspect, unplug the cart if oil is present, connect USB-C fully, watch the indicator, disconnect at full. "
            "That sequence prevents most 'dead battery' tickets we see from filling partners."
        ),
        h3("Before you plug in"),
        ol([
            "Wipe the 510 contact and USB-C port with a dry lint-free cloth. No flooding with alcohol into the port.",
            "If the cart has oil on the pin, remove the cart. Oil on the thread is a short risk.",
            "Place the battery on a hard, non-fabric surface. Do not charge under a pillow or inside a closed metal tin.",
            "Use the same USB-C cable you qualified. Frayed cables cause intermittent charge that looks like a failing cell.",
        ]),
        h3("During and after charge"),
        p(
            "The LED or icon should change in a documented way for that SKU. "
            f"Stylo, Fino, Sandwave, Go Stik, Palm Pro, and M3B Plus do not share one blink language. Read the {a(U['stylo'], 'Stylo battery')} notes and the matching SKU card."
        ),
        p(
            "Disconnect at full. If the pack is too hot to hold comfortably, stop and isolate it. "
            "Do not throw a hot pack in a returns bin with other lithium cells."
        ),
        hr(),
        h2("Why a 510 battery refuses to charge"),
        p(
            "Refusal to charge is usually contact, firmware lockout, or a cell the board has marked unsafe. "
            "Opening the shell to 'test with a bench supply' is not a field fix and voids any replacement path."
        ),
        h3("Cartridge shorts and oily pins"),
        p(
            "Oil across the 510 pin can present as a short. The board may refuse to charge until the load is gone. "
            f"Clean the pin, stand the cart upright, and retry without the cart. Persistent pin oil is a {a(U['b_leak'], 'cartridge leak')} problem, not a charger problem."
        ),
        p(
            "If several batteries from the same tote fail together, inspect the tote for leaked oil before you open an RMA on the cells."
        ),
        h3("Deep discharge and storage"),
        p(
            "Packs stored empty for months may sit below the voltage the charger will wake. "
            "Some boards recover after a short USB-C sit. Some do not. That is a cell protection trip, not a cable issue."
        ),
        p(
            "Warehouse rule: do not store 510 batteries at 0% as a theft-prevention trick. "
            "Partial charge in a dry cabinet beats a tote of unrecoverable packs."
        ),
        hr(),
        h2("Charging with a cartridge attached"),
        p(
            "Pass-through charging (use while plugged in) exists on some boards and not others. "
            "Even when it works, filling partners should treat it as a consumer convenience, not a production test method."
        ),
        h3("When to leave the cart on"),
        p(
            "Leave the cart on only if you are confirming pass-through for a retail instruction card. "
            "Keep the unit upright. A cart on its side while charging can push oil toward the airway as the pack warms."
        ),
        p(
            "If the cart is a live-resin fill, prefer charging the battery alone. "
            "Warm oil plus a warm board is a leak test you did not intend."
        ),
        h3("What retail copy should say"),
        ul([
            "Charge on a hard surface with the supplied or specified USB-C cable.",
            "Remove the cartridge if you see oil on the thread.",
            "Stop if the battery is hot, swollen, or smells sharp.",
            "Do not use a damaged cable or a wet port.",
        ]),
        p(
            "Keep that copy shorter than a novel. People skip manuals. The carton panel still needs the hot-pack stop rule."
        ),
        hr(),
        h2("Common charging mistakes we still see"),
        p(
            "These are not exotic. They show up in the same three emails every quarter. "
            "Fixing them is cheaper than a replacement box."
        ),
        h3("Multi-head chargers and random bricks"),
        p(
            "A 65 W laptop charger is not automatically kinder. "
            "If the pack's board is simple, it wants 5 V. Qualify one brick per warehouse and tape the SKU to it."
        ),
        p(
            "Wireless pads do nothing for these batteries. There is no coil to couple. "
            "If a retailer asks, the answer is USB-C only."
        ),
        h3("Charging in a vehicle cup holder"),
        p(
            "Cabin heat plus a USB port is a poor environment. "
            "Summer cars exceed the storage range printed on most lithium cells. Charge indoors, then travel."
        ),
        p(
            f"The same heat that ruins a charge also thins oil in a seated cart. "
            f"That is how a 'battery issue' turns into a {a(U['g_fill'], 'leak after fill')} complaint on arrival."
        ),
        hr(),
        h2("510 charging versus pod-system charging"),
        p(
            "510 batteries and pod batteries both use lithium-ion cells and USB-C on current Justccell SKUs. "
            "The difference is the load: a 510 pack may see many cart resistances over its life. A pod pack sees one family."
        ),
        table(
            ["System", "Charge habit", "Watch for"],
            [
                ["510 battery", "Often charge without cart", "Oily 510 pin shorts"],
                ["Pod battery", "Usually charge assembled", "Pod seating and contacts"],
                ["All-in-one", "Charge the finished unit", "Oil in USB-C port after a leak"],
            ],
        ),
        h3("All-in-ones need a dry USB-C port"),
        p(
            f"On a device such as {a(U['tank'], 'Tank')}, a leak can reach the charge port. "
            "If oil is in USB-C, do not force a cable. That is a contamination issue, not a 'charge faster' issue."
        ),
        p(
            "Wipe, isolate, and replace. Pushing a cable into an oily port bends the tongue and kills the board."
        ),
        h3("Replacement path"),
        p(
            "Do not open the shell. Request a replacement through your account contact with the SKU, lot, and a photo of the indicator during charge. "
            "A photo of the oily pin helps more than a paragraph of adjectives."
        ),
        hr(),
        h2("Put charge rules on the sell sheet"),
        p(
            "If you ship hardware with a battery, print the charge stop-rules next to voltage. "
            f"Then send the oil and battery pairing when you {a(U['contact'], 'request a Justccell quote')} so the sample pack matches the instruction card."
        ),
        h3("Minimum charge line for a carton"),
        ul([
            "USB-C, 5 V source unless the SKU card says otherwise.",
            "Hard surface, dry port, disconnect at full.",
            "Remove leaking cartridges before charging.",
        ]),
        h3("What we still need from you"),
        p(
            "Tell us which battery SKU sits in the retail kit. Fino's dock behaviour is not Stylo's pen behaviour. "
            "One generic 'charge overnight' line is how you get swollen returns."
        ),
        p(
            f"If you are still choosing a battery family, read {a(U['g_510'], 'what a 510 thread is')} first, then pick voltage steps that match the oil."
        ),
    ])


def guide_510() -> str:
    return "\n".join([
        p(
            "510 is the common screw connection between a cartridge and a battery. "
            f"If both parts are specified as 510, they are meant to mate on that thread. {a(U['thread'], 'Justccell 510 notes')} exist because '510' on a box is not the whole spec."
        ),
        p(
            "The name comes from the thread form: a 10-thread, 0.5 mm-pitch connector used across oil carts and many batteries. "
            "It is a mechanical standard, not a guarantee that every voltage, diameter, or pin length will behave."
        ),
        bq(
            "In Short",
            "A 510 thread is a screw interface between cart and battery, not a quality grade. "
            "Match thread, pin contact, and voltage to the oil. "
            "Pods and all-in-ones are different systems even when they sit next to 510 SKUs in a catalog.",
        ),
        h2("What is a 510 thread?"),
        p(
            "A 510 thread is a male/female screw pair that carries mechanical hold and, through the centre pin and shell, electrical contact. "
            "Tighten until seated. Do not crank as if it were plumbing. Over-torque cracks mouthpieces and lifts centre pins."
        ),
        p(
            "On Justccell 510 carts the thread is on the cartridge base. On the battery it is the well. "
            "Cross-threading shows up as a cart that sits at an angle and a pin that never quite kisses."
        ),
        h3("Thread versus pin"),
        p(
            "The thread holds. The centre pin and its spring or pad pass current. "
            "A perfect thread with a recessed pin gives no hit. A proud pin with a short thread can still fire while leaking oil around the shoulder."
        ),
        p(
            "When a cart 'does not work' on a known-good battery, check pin height and oil on the contact before you blame the board."
        ),
        h3("Why 510 won oil hardware"),
        p(
            "Filling lines like a commodity thread. Retailers like a battery they already stock. "
            "That is why 510 remains the default for open carts even as pods grow in closed systems."
        ),
        p(
            "Commodity is also the risk. Anyone can stamp 510 on a drawing. "
            "Pore size, glass, and seals are where carts actually differ."
        ),
        hr(),
        h2("Which Justccell parts use 510"),
        p(
            "Cartridges in the 510 family include Ceramic-EVOMAX, TH2-EVOMAX, M6T-EVOMAX, TH2-SE, and M6T-SE. "
            "Batteries include Stylo, Fino, Sandwave, Go Stik, Palm Pro, M3B Plus, and M3 Plus."
        ),
        p(
            f"All-in-ones and pods are not 510 drop-ins. {a(U['pod'], 'Pod systems')} keep their own cartridge keys."
        ),
        h3("Glass versus thermoplastic carts"),
        p(
            f"{a(U['th2'], 'TH2-EVOMAX')} uses a borosilicate glass body. M6T-EVOMAX uses a BPA-free thermoplastic body. "
            "Both are 510. They do not share the same thermal mass or drop behaviour."
        ),
        p(
            "Glass shows oil colour well and chips if you run a careless capper. "
            "Thermoplastic survives some handling abuse and can scuff. Pick for the line, not the lightbox."
        ),
        h3("Batteries are not interchangeable by thread alone"),
        p(
            "A 190 mAh Fino cell plus dock is not a 500 mAh Stylo pen. "
            "Voltage steps differ. A cart that tastes clean at 2.4 V on Stylo can scorch on a fixed-voltage mystery battery from a drawer."
        ),
        p(
            "If you ship a cart without a battery, print a voltage range on the insert. "
            "510 compatibility without voltage guidance is how live resin gets cooked."
        ),
        hr(),
        h2("How to mate a 510 cart and battery"),
        p(
            "Seat straight, turn until snug, then test at the lowest voltage. "
            "If the cart wobbles, back off and restart. Forcing a cross-thread feels like tightness and still fails electrically."
        ),
        h3("Shop-floor sequence"),
        ol([
            "Inspect the thread for plastic flash or dried oil.",
            "Hold the battery vertical. Start the cart by hand, not with a driver.",
            "Stop when the shoulder meets the well. No extra half turn 'for luck'.",
            "Fire a short pulse at the lowest setting. Listen for a crackle that means a wet short.",
        ]),
        h3("Diameter and tank length still matter"),
        p(
            "510 does not fix cart diameter. A wide glass tank can collide with a recessed battery well or a magnetic sleeve. "
            "Measure the well on Palm Pro versus a slim pen before you print a 'fits all 510' claim."
        ),
        p(
            "Long 1.2 ml tanks on short batteries look top-heavy and invite drops. "
            "That is a retail design problem, not a thread problem."
        ),
        hr(),
        h2("Common 510 failures that are not 'the thread'"),
        p(
            "People blame 510 because it is the visible joint. "
            "Most field failures are oil, voltage, or pin geometry."
        ),
        h3("Oil on the contact"),
        p(
            "A leaking cart paints the pin. The battery then looks dead or charges oddly. "
            f"Clean the well, isolate the cart, and treat it as a {a(U['g_fill'], 'fill and cap')} issue."
        ),
        p(
            "Do not scrape the pin with a knife. You will flatten the contact and create a new intermittent."
        ),
        h3("Wrong voltage on a correct thread"),
        p(
            f"Thread match plus a 3.6 V step on rosin is still a failed experience. "
            f"{a(U['g_volt'], 'Set voltage to the oil')}, then talk about 510."
        ),
        ul([
            "Live extracts: lowest step first.",
            "Distillate: mid step after a prime.",
            "Fixed-voltage batteries: qualify them as a SKU, do not assume.",
        ]),
        hr(),
        h2("510 versus closed systems"),
        p(
            "Closed systems trade the universal thread for a keyed pod or a welded all-in-one. "
            "You gain control of heater and tank. You lose the ability to mix third-party batteries."
        ),
        table(
            ["System", "Connection", "You control"],
            [
                ["510 cart + battery", "Screw thread", "Cart SKU and battery SKU separately"],
                ["Pod system", "Keyed pod", "Matched heater and board"],
                ["All-in-one", "Built together", "Whole device as one unit"],
            ],
        ),
        h3("When 510 is the right call"),
        p(
            "Use 510 when retail already sells batteries, when you need several tank materials, or when you want to trial cores without new injection tools. "
            "It is the fastest path from sample to a filling line that already has 510-capable cappers."
        ),
        p(
            f"Use a closed device when you need child-resistant geometry, laser-ready bodies, or a heater that never meets a random battery. "
            f"{a(U['n_cr'], 'Child-resistant hardware')} often lives on all-in-ones for that reason."
        ),
        h3("What 510 will not do"),
        p(
            "It will not make a cotton core kind to rosin. It will not make a thin live resin stay put in an overfilled tank. "
            "It will not satisfy a market that bans the finished consumer product you have in mind. That is your licence."
        ),
        hr(),
        h2("Specify 510 like an engineer, not a hashtag"),
        p(
            "On a quote request, write 510 cart family, tank material, fill volume, and the battery you will ship or recommend. "
            f"Then {a(U['contact'], 'send that brief to Justccell')} with the oil type."
        ),
        h3("Minimum 510 line on a PO"),
        ul([
            "Cart SKU and tank volume.",
            "Battery SKU or 'cart only, voltage range printed'.",
            "Oil family and fill temperature.",
        ]),
        h3("Related hardware notes"),
        p(
            f"If you are still choosing oil family, use {a(U['g_oil'], 'hardware by oil type')} before you freeze the thread. "
            "510 is the joint. The oil is the product."
        ),
    ])
