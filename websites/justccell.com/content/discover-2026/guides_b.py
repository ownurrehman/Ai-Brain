"""Remaining Guides plus extra sections to clear 2000 words."""

from lib import U, a, bq, h2, h3, hr, ol, p, table, ul


def extra_oil() -> str:
    return "\n".join([
        h2("How filling temperature changes the hardware brief"),
        p(
            "Fill temperature is part of the hardware spec. "
            "Oil that is easy to meter at 55 C can sit like syrup at 18 C in a van, then thin again on a shop heater."
        ),
        p(
            "If you warm oil to fill, you must also plan the cool-down with the cap on. "
            "Warm oil in an open tank climbs the chimney. That is not a mystery leak. That is physics you scheduled."
        ),
        h3("Ask the lab for two viscosities"),
        p(
            "Request viscosity at fill temperature and at 20 C on the same lot. "
            "A single 'thick distillate' label hides a 2x swing that will pick the wrong airway."
        ),
        p(
            "If the lab cannot run a viscometer, they can still record draw time through a named needle gauge at both temperatures. "
            "Crude is better than a tasting note."
        ),
        h3("Headspace after a warm fill"),
        p(
            "Warm fills expand. Leave headspace so the cool cart does not pressurise the mouthpiece. "
            "A tank that looks 'short' on the line at 50 C can be correct at room temperature."
        ),
        p(
            "Operators hate short fills because they look like under-delivery. "
            "Print the cool mass on the SOP, not the warm visual line."
        ),
        h2("All-oil SKUs and when they still fail"),
        p(
            "All-oil-capable hardware is a wider window, not a magic window. "
            "Flexcell X, Tank, Eco Star, Vision Box, Voca Pro Max, and Voca Max sit in that Justccell group because the core and seals are specified for mixed programmes."
        ),
        p(
            "They still fail if you overfill, cap late, or run 3.6 V on a terpene-heavy lot. "
            "All-oil means 'more than one family on a good day', not 'ignore the SOP'."
        ),
        h3("When to split SKUs anyway"),
        p(
            "Split SKUs when one oil is solventless and the other is high-cut distillate. "
            "The retail box can look similar. The core should not."
        ),
        p(
            "If marketing wants one hero device, pick the harder oil as the design driver. "
            "A rosin-safe core usually survives distillate. The reverse is a common regret."
        ),
        h3("Documentation that belongs on the SKU card"),
        ul([
            "Oil family and lot.",
            "Fill mass and headspace rule.",
            "Cap delay maximum in seconds.",
            "Battery model and starting voltage.",
            "24-hour leak check orientation.",
        ]),
    ])


def extra_charge() -> str:
    return "\n".join([
        h2("How mAh ratings relate to a charge session"),
        p(
            "mAh is a capacity label, not a charge-time promise. "
            "A 190 mAh Fino cell fills faster than a 500 mAh Stylo cell on the same 5 V brick, assuming the board allows it."
        ),
        p(
            "Docks change the story. Fino pairs a small stick cell with a 1000 mAh dock. "
            "Charging the dock is not the same job as charging the stick. Keep those instructions separate on the carton."
        ),
        h3("Do not mix mAh with voltage"),
        p(
            "A bigger cell does not mean a hotter coil. Voltage at the cart is a board setting. "
            "Swapping to a higher mAh battery because 'it hits harder' is how you get confused retail staff."
        ),
        p(
            "If you need harder hits, that is oil, airflow, or a voltage step. "
            "Capacity only decides how long until the next USB-C session."
        ),
        h3("Indicator language on a mixed kit"),
        p(
            "If a kit can include more than one battery SKU, do not print one LED colour key. "
            "Print 'see battery card' or ship one SKU per kit."
        ),
        p(
            "Mixed kits are where customers plug a cable into a full pack and decide the product is broken. "
            "Your ticket queue already knows this."
        ),
        h2("Safety stops that belong in warehouse SOPs"),
        p(
            "Lithium-ion 510 packs are small. They still swell, heat, and fail. "
            "A tote of 200 units is a tray of cells, not a box of pens."
        ),
        h3("Isolate damaged packs"),
        ol([
            "Swollen body, hiss, or sharp solvent-like smell: isolate in a non-flammable container.",
            "Do not pocket a hot pack to 'see if it calms down'.",
            "Do not charge a pack that already failed a short test with an oily pin until the pin is clean and the cart is quarantined.",
            "Log SKU, lot, and photo. That is the replacement file.",
        ]),
        h3("Training one sentence"),
        p(
            "If it is hot, unplug it. If it is oily, unplug the cart. If it is swollen, do not charge it. "
            "That sentence belongs on the wall above the charge strip."
        ),
        p(
            f"Charge discipline sits next to {a(U['safety'], 'Justccell safety notes')} for a reason. "
            "Hardware safety is not only the oil path. It is also the cell you ship beside it."
        ),
    ])


def extra_510() -> str:
    return "\n".join([
        h2("How 510 pin height shows up on a filling line"),
        p(
            "Pin height variation is a batch issue. "
            "A cart that fires on Stylo and dies on a deep-well battery is often a pin that never reaches the pad."
        ),
        p(
            "Keep one known-good battery at incoming inspection. "
            "If a whole carton fails that battery, stop the line. Do not 'try another pen from the drawer'."
        ),
        h3("Spring pins versus fixed pads"),
        p(
            "Some batteries use a sprung centre pin. Some use a flat pad. "
            "A short cart pin may still fire on a sprung well and fail on a pad. That is not random. That is geometry."
        ),
        p(
            "Write the test battery SKU on the incoming SOP. "
            "Changing the test pen every Monday creates ghost defects."
        ),
        h3("Torque is a process spec"),
        p(
            "Hand-tight plus a defined extra angle is a spec. "
            "Air drivers on 510 threads crush mouthpieces and lift pins. If you automate assembly, use a torque-limited tool."
        ),
        p(
            "Cracked ceramic mouthpieces after capping often get blamed on the kiln. "
            "Check the driver first."
        ),
        h2("510 drawings versus what you receive"),
        p(
            "A PDF that says 510 is a claim. Incoming inspection is the control. "
            "Measure thread start, pin height, and tank diameter on the first carton of every lot."
        ),
        table(
            ["Check", "Why it exists", "Fail signal"],
            [
                ["Pin height", "Electrical contact", "No fire on known battery"],
                ["Shoulder seat", "Oil seal at well", "Wobble or oil ring"],
                ["Tank diameter", "Sleeve and well clearance", "Cart will not sit flush"],
            ],
        ),
        h3("Keep a golden sample"),
        p(
            "Tape a passed cart and battery pair in the QC cabinet. "
            "Arguments end faster when the golden sample is on the bench."
        ),
        p(
            f"If you need a different tank material in the same thread family, compare {a(U['ceramic'], 'Ceramic-EVOMAX')} against TH2 and M6T on that golden battery, not on a random retail pen."
        ),
        h3("Retail claims to avoid"),
        p(
            "Do not print 'fits all 510 batteries ever made'. "
            "Print '510 thread, test on [battery SKU], recommended voltage [range]'."
        ),
        p(
            "Universal thread plus a voltage range is honest. "
            "Universal thread plus silence is how live resin gets a 4 V box-mod story."
        ),
    ])


def guide_fill() -> str:
    return "\n".join([
        p(
            "Most cartridge leaks are fill and cap errors, not mysterious ceramic defects. "
            f"Oil in the chimney, late mouthpieces, and zero headspace show up before a pore ever fails. {a(U['cartridge'], 'Justccell cartridges')} still need a filling SOP that matches the tank."
        ),
        p(
            "A ceramic core can wick perfectly and still weep if you overfill a warm tank and cap it two minutes later. "
            "Treat fill like a timed process, not a pour."
        ),
        bq(
            "What You'll Learn",
            "Fill to a measured mass with headspace, then cap on the same short clock. "
            "Keep tanks upright while oil settles. "
            "Late caps and sideways trays cause more leaks than 'bad ceramic' stories.",
        ),
        h2("What a leak actually is on a 510 cart"),
        p(
            "A leak is oil leaving the reservoir through the airway, the mouthpiece joint, or the 510 shoulder. "
            "Those three paths have different causes. Naming the path is the first QC skill."
        ),
        p(
            "Mouthpiece oil after a day upright is usually chimney flood from overfill or delayed cap. "
            "Oil on the 510 pin is often a base seal or a cart stored on its side in a warm tote."
        ),
        h3("Chimney flood"),
        p(
            "The chimney is the tube the vapour travels through. If liquid oil sits in it at cap time, the first draw is a spit of oil. "
            "Warm fills raise oil and then drop it as the tank cools. Cap while the geometry is still the fill geometry."
        ),
        p(
            "If you must wait, wait with a temporary cover that is not the final mouthpiece, then recap to spec. "
            "An open tank in a dusty room is a different failure."
        ),
        h3("Shoulder and pin oil"),
        p(
            "Oil at the 510 shoulder means the base joint or the well is seeing liquid. "
            "That can be a cracked glass tank, a bad gasket, or storage on the side. Do not keep filling a lot that paints every pin."
        ),
        p(
            f"Pin oil also kills batteries. Clean the well and read {a(U['g_charge'], '510 charging notes')} before you scrap the pack."
        ),
        hr(),
        h2("How to fill a ceramic cartridge without flooding it"),
        p(
            "The working sequence is: warm oil to the SOP temperature, dispense a weighed shot, leave headspace, cap inside the time window, stand upright to cool. "
            "Skip one of those and you will invent a hardware complaint."
        ),
        h3("Weigh the shot"),
        p(
            "Visual fill lines lie when oil is warm, dark, or foamy. "
            "Weigh the empty cart, dispense, weigh again. Hold a tolerance in milligrams that your filler can actually hit."
        ),
        p(
            "If foam sits on top, wait for it to collapse before you judge the line. "
            "Capping on foam traps gas that later shoves oil up the chimney."
        ),
        h3("Headspace is not wasted volume"),
        p(
            "Headspace is the expansion joint. Live resin and any warm fill need it. "
            "A 'full to the brim' photo for Instagram is a leak generator."
        ),
        ol([
            "Set target mass from the SKU card, not from a pretty photo.",
            "Stop the needle before the chimney inlet.",
            "Wipe the needle so you do not drip into the airway on exit.",
            "Cap within the SOP window. If you miss it, treat the unit as quarantine, not retail.",
        ]),
        hr(),
        h2("Why cap timing matters more than people admit"),
        p(
            "Uncapped oil sees air, dust, and a chimney that is happy to act as a straw. "
            "Every extra minute is a chance for oil to wet the airway walls."
        ),
        p(
            "Filling partners who cap on the same shift still fail if the cart sits 20 minutes in a rack. "
            "Same shift is not a spec. Seconds to a few minutes is a spec."
        ),
        h3("Build a cap clock"),
        p(
            "Start the clock at last drop, not at 'tray full'. "
            "A 50-cart tray filled over 12 minutes already has a spread. Cap in fill order, not in pretty rows."
        ),
        p(
            "If the capper is slower than the filler, slow the filler. "
            "Speed on the dispenser with a queue of open tanks is how you buy leak rates."
        ),
        h3("Mouthpiece style changes the clock"),
        p(
            "Press-on mouthpieces are fast and unforgiving. Screw-on tops can be slower and still seal if the gasket is seated. "
            "Do not mix styles on one SOP without rewriting the time window."
        ),
        p(
            "If laser-ready tops need orientation, that is extra handling. "
            f"Add time for {a(U['laser'], 'laser engraving alignment')} or engrave before fill."
        ),
        hr(),
        h2("Storage, transit, and the leak you did not create on the line"),
        p(
            "A perfect fill can still leak in a hot van on its side. "
            "QC that only checks the line at 22 C will miss field leaks."
        ),
        h3("Orientation"),
        p(
            "Upright is the default. Sideways is an abuse test you should run on purpose, not by accident in a padded envelope. "
            "If retail will toss carts in a bag, test that. Then either change the pack or change the claim."
        ),
        p(
            f"{a(U['pack'], 'Packaging')} is part of leak control. Inserts that hold the tank vertical earn their cost."
        ),
        h3("Heat and altitude"),
        p(
            "Cabin heat thins oil. Air freight drops external pressure. Both push liquid toward the chimney. "
            "If you ship filled hardware by air, test a vacuum or altitude cycle on the packed SKU."
        ),
        p(
            "Empty hardware is kinder to ship. If you can fill in-market, do. "
            "Filled goods need a pack that assumes a worst-case hold."
        ),
        hr(),
        h2("Common fill mistakes on ceramic cores"),
        p(
            "Ceramic does not forgive a wet chimney. It also does not cause every wet chimney. "
            "Sort the mistake before you change pore size."
        ),
        h3("Needle in the chimney"),
        p(
            "Operators aiming for speed sometimes park the needle in the airway. "
            "That coats the vapour path with oil before the consumer ever draws. Train the aim at the reservoir wall."
        ),
        p(
            "A bent needle from a previous jam will keep hitting the chimney. "
            "Replace needles on a count, not when someone notices the drip."
        ),
        h3("Prime versus flood"),
        p(
            "A short, low-voltage prime after cap can wet the core. A long button-hold on a full chimney blasts oil. "
            "Prime on a passed, capped cart. Do not 'test fire' open tanks."
        ),
        ul([
            "No firing until the mouthpiece is seated.",
            "Lowest voltage, short pulse.",
            "Spit of oil means the unit is already a leak, not a flavour test.",
        ]),
        hr(),
        h2("Ceramic fill versus cotton-core fill"),
        p(
            "Cotton cores hold a reservoir of oil in fibre. Ceramic cores hold oil in pores and the tank. "
            "Flooding a ceramic chimney is obvious. Flooding cotton can hide as a 'wet hit' that people blame on the oil."
        ),
        table(
            ["Core", "Fill sensitivity", "Typical tell"],
            [
                ["Ceramic", "Chimney and headspace", "Oil in mouthpiece, clean pin or not"],
                ["Cotton", "Dry hits if under-wetted", "Burnt note after a hot fire"],
                ["Either", "Late cap", "Overnight weep"],
            ],
        ),
        h3("Why we still specify ceramic for thick oils"),
        p(
            f"Ceramic pores move viscous oil without a fibre that chars. "
            f"That is the point of {a(U['b_vs'], 'ceramic versus cotton heating')} on cannabis extracts."
        ),
        p(
            "Ceramic does not remove the need for mass, headspace, and a cap clock. "
            "It removes a fibre that burns when the SOP is already sloppy."
        ),
        h3("QC sample size"),
        p(
            "Check more than one cart per tray. Three weeping units is a process. One weeping unit can be a missed cap. "
            "Log both. If you only log the pretty ones, your leak rate is fiction."
        ),
        hr(),
        h2("Write the fill SOP before you lock artwork"),
        p(
            "Artwork that shows a brim-full tank fights the SOP. Change the photo. "
            f"When the process is stable, {a(U['contact'], 'request production hardware')} with the fill mass and cap window on the brief."
        ),
        h3("SOP lines that prevent arguments"),
        ul([
            "Target mass and tolerance.",
            "Oil temperature at dispense.",
            "Maximum seconds from last drop to seated mouthpiece.",
            "Upright cool-down time before packing.",
            "Leak check at 24 hours, sample size named.",
        ]),
        h3("If leaks persist on a passed SOP"),
        p(
            "Then you have a hardware lot issue: gasket, crack, or pore. Stop and open a lot file with photos of the path (mouthpiece vs pin). "
            f"Bring that file when you talk to us. It is more useful than {a(U['g_oil'], 'picking a new silhouette')} at random."
        ),
        p(
            "Justccell quotes empty hardware. Your fill process is yours. "
            "We still want the failure path so the next sample tray is not a repeat."
        ),
        h2("Needle gauge, oil temperature, and foam"),
        p(
            "Needle gauge is a leak tool. Too thin and you drip. Too wide and you jet into the chimney. "
            "Match gauge to viscosity at the fill temperature you actually run, not the temperature on last month's SOP printout."
        ),
        p(
            "If the oil foams, you are injecting air or you are too hot. Foam is not a fill line. "
            "Pause, let it fall, then judge mass. Capping foam is how you ship air springs that later pump oil."
        ),
        h3("One oil, one needle"),
        p(
            "Do not leave a 14-gauge on the bench for 'whatever comes next'. "
            "Tag needles by oil family. Distillate and live resin should not share a mystery needle in a cup of IPA."
        ),
        p(
            "IPA on a needle is fine. IPA dripping into a tank is not. Dry the needle. "
            "Solvent in the first millimetre of oil becomes a consumer complaint you will never reproduce in QC."
        ),
        h3("Bench versus automated lines"),
        p(
            "Hand fills miss mass. Automated lines miss alignment. "
            "Both need the same headspace rule. Automation just lets you miss it faster."
        ),
        p(
            "If you automate, instrument the cap clock. A human can see an open tray. A robot will happily queue 200 open tanks."
        ),
    ])


def extra_fill() -> str:
    return ""


def guide_voltage() -> str:
    return "\n".join([
        p(
            "Voltage is how you set coil temperature on a 510 battery. "
            f"Live resin, live rosin, and distillate do not share one comfortable step. {a(U['battery'], 'Justccell 510 batteries')} with 2.4 V to 3.6 V steps exist so you can match the oil instead of cooking it."
        ),
        p(
            "Start at the lowest supported step on a new cart. Increase only if vapour is thin and flavour is still clean. "
            "A burnt note is a stop, not a cue to push higher."
        ),
        bq(
            "What You'll Learn",
            "Treat voltage as an oil spec, not a cloud contest. "
            "Live extracts want the low end of a variable-voltage 510 battery. Distillate can sit higher after a prime. "
            "Fixed-voltage batteries must be qualified as their own SKU.",
        ),
        h2("What voltage does inside a cartridge"),
        p(
            "Voltage across the heater sets power, which sets temperature once resistance and airflow are in play. "
            "You do not set Celsius on most 510 pens. You set a step and you infer temperature from flavour and vapour."
        ),
        p(
            "A ceramic core still has hot spots if the board dumps too much power. "
            "Even heat is a core design. Voltage is how you abuse or respect that design."
        ),
        h3("Why 'higher is stronger' fails"),
        p(
            "Higher voltage can make a bigger cloud and a harsher throat. It also strips monoterpenes first. "
            f"If the brand paid for a live-resin fraction, high voltage is how you turn it into a generic hot oil. See {a(U['b_terp'], 'why terpenes care about heat')}."
        ),
        p(
            "Strength of effect is mostly the oil. Voltage is delivery. "
            "Do not use voltage to hide a weak formulation."
        ),
        h3("Resistance still matters"),
        p(
            "Two 510 carts at 3.2 V are not the same if their heaters differ. "
            "Qualify voltage on the actual SKU. Do not copy a blog range onto a different ceramic geometry."
        ),
        p(
            "If you change cart family, re-run the voltage ladder on that oil lot. "
            "This is a short test. Skipping it is how a 'known range' becomes a return spike."
        ),
        hr(),
        h2("How to set voltage by extract family"),
        p(
            "Use the cart maker's card first. If the card is silent, start low on a variable-voltage Justccell battery and step up with notes. "
            "Published ranges in consumer blogs conflict. Your oil lot wins."
        ),
        table(
            ["Oil family", "Starting step", "Why"],
            [
                ["Live rosin", "Lowest (often 2.4 V)", "Delicate, clogs and scorches early"],
                ["Live resin", "Low (2.4-2.8 V band)", "Terpene-forward, heat-sensitive"],
                ["Distillate", "Low, then mid (2.8-3.3 V)", "Needs heat to move, still burns if rushed"],
            ],
        ),
        h3("Live resin and rosin"),
        p(
            "These oils are why low-temp ceramic exists. "
            f"{a(U['three'], 'Justccell 3.0 heating')} is specified to drop peak temperature. A 3.6 V retail pen can undo that work in one session."
        ),
        p(
            "If flavour goes flat or peppery-hot, you are past the useful band. "
            "Do not keep firing to 'clear the clog'. Cool down, check for a flooded chimney, then resume low."
        ),
        h3("Distillate"),
        p(
            "Distillate often needs more heat to produce a satisfying vapour. "
            "That is not permission to start at maximum. Prime low, then step to the mid band if vapour is weak."
        ),
        p(
            "Thick distillate may want the top step on a 2.8/3.2/3.6 V switch. "
            "Confirm on the lot. A winter warehouse distillate is not a summer warehouse distillate."
        ),
        hr(),
        h2("How to run a voltage ladder on a sample tray"),
        p(
            "A voltage ladder is a written test: same oil, same cart SKU, same battery SKU, three steps, notes on flavour, vapour, and heat of the body. "
            "It takes an afternoon. It saves a season of tickets."
        ),
        h3("Ladder protocol"),
        ol([
            "Use a fully charged battery so the first step is not a sagging cell.",
            "Draw at the lowest step for several sessions, not one impatient hit.",
            "Step up only if vapour is thin and flavour is still true to the jar.",
            "Stop immediately on burnt, cracked pepper, or a body too hot to hold.",
            "Record the winning step on the SKU card and the retail insert.",
        ]),
        h3("Who owns the winning step"),
        p(
            "Operations owns the number that passed leak and flavour. Marketing owns the sentence on the box. "
            "If those disagree, the box is wrong."
        ),
        p(
            "Print a range if you must. Print a start point if you can. "
            "'Start low' is a better line than a decorative lightning icon."
        ),
        hr(),
        h2("Fixed-voltage batteries and why they are a SKU"),
        p(
            "A fixed-voltage 510 battery is simpler to sell and harder to tune. "
            "You cannot 'start low' if there is no low. Qualify that pack on the oil or do not ship it with live resin."
        ),
        h3("When fixed voltage is acceptable"),
        p(
            "Distillate programmes with a known cart resistance can live on a fixed pack you tested. "
            "Gift SKUs and mystery drawer batteries cannot."
        ),
        p(
            "If retail partners will mix batteries, you do not have a fixed-voltage programme. "
            "You have a 510 thread and a hope. Hope is not a spec."
        ),
        h3("Variable steps on Justccell pens"),
        p(
            "Stylo lists 2.4 / 2.8 / 3.2 V. Sandwave and Palm Pro use slide or button steps in a similar neighbourhood. "
            "Fino offers a wider set of steps with the dock system. Read the SKU card. Do not average them into one mythic 3 V."
        ),
        p(
            f"Match the battery to the cart family you already chose in {a(U['g_oil'], 'the oil-first hardware map')}."
        ),
        hr(),
        h2("Common voltage mistakes"),
        p(
            "These mistakes show up as 'the new batch tastes burnt' even when the oil lab sheet is clean."
        ),
        h3("Testing on a depleted battery"),
        p(
            "A dying cell sags under load. The first hit feels weak, so someone cranks the step, then charges the pack and scorches the core. "
            "Ladder tests use a charged pack. Always."
        ),
        p(
            "Warehouse demos on random leftover batteries are how you train sales on the wrong number."
        ),
        h3("Clearing clogs with max voltage"),
        p(
            "A clog can be cold oil, a flooded chimney, or a failed core. Max voltage turns two of those into a burnt core. "
            f"Warm the device gently, check fill faults in {a(U['g_fill'], 'the leak and fill guide')}, then resume low."
        ),
        ul([
            "Do not hold the button for a long 'boost'.",
            "Do not dual-wield with a box mod above the cart's intended band.",
            "Do not tell customers to 'crank it' as a clog policy.",
        ]),
        hr(),
        h2("Voltage versus temperature-labelled devices"),
        p(
            "Some pods expose temperature or watt steps instead of volts. "
            f"{a(U['luster'], 'Luster Pro')} and Dart-style systems are closed. Their numbered modes are not 510 voltage numbers. Do not copy a 2.8 V cart note onto a pod mode called '3'."
        ),
        table(
            ["Device class", "You adjust", "Do not copy from"],
            [
                ["510 battery", "Voltage steps", "Pod mode numbers"],
                ["Pod system", "Named modes / watts", "510 volt blogs"],
                ["All-in-one", "Often little or none", "Either of the above"],
            ],
        ),
        h3("All-in-ones hide the number"),
        p(
            "Many all-in-ones fire at a designed band. Your job is to pick the device for the oil, not to retune it. "
            "If the all-in-one is harsh on live resin, change the SKU, do not tell staff to 'draw shorter' as a full solution."
        ),
        p(
            "Shorter draws can help. They do not replace a heater that runs too hot for that oil."
        ),
        h3("Honest limitation"),
        p(
            "Without a thermocouple in the core, every public voltage chart is an estimate. "
            "We use them as start points. Your ladder on your lot is the control."
        ),
        hr(),
        h2("Put the winning voltage on the quote"),
        p(
            "When you request samples, name the oil and the battery you will ship. "
            f"{a(U['contact'], 'Justccell quotes')} are faster when we do not have to guess whether the cart will meet a 2.4 V pen or a mystery stick."
        ),
        h3("Insert copy that helps"),
        ul([
            "Start on the lowest setting.",
            "Increase only if vapour is thin.",
            "Stop if flavour turns burnt or the device is hot.",
        ]),
        h3("Next hardware decisions"),
        p(
            f"If the thread itself is still fuzzy, read {a(U['g_510'], 'what a 510 thread is')}. "
            "Voltage sits on top of a seated pin, not instead of it."
        ),
        h2("Preheat modes and when they help"),
        p(
            "Preheat is a short low-power pulse to loosen thick oil. "
            "It helps winter distillate. It can flood a live-resin chimney if the cart was overfilled."
        ),
        p(
            "Treat preheat as a timed function, not a second flavour mode. "
            "If a device has no preheat, a short low-voltage draw does similar work without a marketing name."
        ),
        h3("Do not preheat a leaking cart"),
        p(
            "If oil is already in the mouthpiece, preheat turns a leak into a spit. "
            "Quarantine that unit. Preheat is for sealed, passed fills."
        ),
        p(
            "Sales staff love preheat because it looks like a feature. "
            "Operations should love it only when the SOP says the oil is thick enough to need it."
        ),
        h3("Board sag and cheap cables"),
        p(
            "A weak USB-C cable that barely charges also leaves you testing voltage on a half-full cell. "
            "Qualify cables next to bricks. This is unglamorous and it removes a whole class of 'the setting feels different today' bugs."
        ),
    ])


def extra_voltage() -> str:
    return ""
