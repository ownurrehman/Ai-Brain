"""Link-free unique extensions so each post clears 2000 words without duplicate URLs."""

from lib import h2, h3, ol, p, ul


def ext(title: str, lead: str, h3a: str, pa: list[str], h3b: str, pb: list[str]) -> str:
    parts = [h2(title), p(lead), h3(h3a), *[p(x) for x in pa], h3(h3b), *[p(x) for x in pb]]
    return "\n".join(parts)


LONG = {
    "g_oil": "\n".join([
        ext(
            "Shift notes when oil lots change mid-week",
            "A lot change is a hardware event. The cart that passed Monday can fail Wednesday if the lab cut more terpenes into the same named SKU.",
            "Yellow tags",
            [
                "Yellow-tag the first 30 units of a new lot and treat them as a mini tray. Do not pour them into the main shipper until the 24-hour check is dry.",
                "If the yellow tag is skipped because the order is late, you are choosing tickets. Write that choice down so it is not a surprise.",
            ],
            "Same name, new behaviour",
            [
                "Brand SKU names stay stable. Oil does not. Never let a stable name talk you out of a scale check.",
                "If purchasing bought a cheaper cut, that is an oil change even when the invoice line looks identical.",
            ],
        ),
        ext(
            "Colour chips versus core chips",
            "A colour chip on a ring does not wick oil. Keep colour samples in a different drawer from heater samples so nobody 'approves hardware' by picking a blue.",
            "Drawer rules",
            [
                "Heater samples stay filled or clearly marked empty-with-lot. Colour chips stay dry.",
                "If a salesperson grabs a heater sample as a colour dummy, they will fire it. Then you lost the golden unit.",
            ],
            "Photo studios",
            [
                "Studio heat lights warm tanks. A leak in studio is still a leak. Do not excuse it as 'lights'.",
                "After studio, those units are not sellable if they were filled. Plan dummy empties for photography.",
            ],
        ),
    ]),
    "g_charge": "\n".join([
        ext(
            "Cable colour codes that actually get used",
            "If every USB-C cable is black, staff will grab the frayed one. Colour the qualified cables and throw the rest in a sealed bin.",
            "Bins",
            [
                "A sealed bin for unknown cables stops 'this one works' experiments on a 200-unit tote.",
                "Once a month, cut the unknown cables so they cannot migrate back. Hoarding cables is not thrift. It is a short factory.",
            ],
            "Laptop ports",
            [
                "Office laptops are the most common unqualified source. Put a sticker on the packing bench: phone brick only.",
                "If a founder insists on charging from a laptop, give them a marked brick anyway. Founders still swell packs.",
            ],
        ),
        ext(
            "Winter and summer charge rooms",
            "A charge room that is 8 C in January and 32 C in July is two different rooms. Lithium cells care.",
            "Temperature log",
            [
                "A cheap logger on the shelf is enough. If the room swings, move the strip.",
                "Do not charge next to the fill warmer. That warmer exists to thin oil, not to babysit cells.",
            ],
            "Condensation",
            [
                "Cold packs brought into a humid fill room sweat. Dry them before USB-C. Water in the port is a board killer.",
                "The same sweat on a 510 well looks like a leak. Check temperature before you scrap a cart lot.",
            ],
        ),
    ]),
    "g_510": "\n".join([
        ext(
            "Cross-threading on a Friday afternoon",
            "Friday speed is where 510 shoulders go crooked. The cart feels tight and still sits at an angle.",
            "Feel versus look",
            [
                "Look down the axis. If you see daylight under one side of the shoulder, back it off. Tight is not seated.",
                "A seated cart should not rattle when you tap the battery. Rattle is a pin or thread story.",
            ],
            "Gloves",
            [
                "Oily gloves hide torque. If operators wear nitrile shiny with oil, they will overtighten. Swap gloves on a count.",
                "Powdered gloves in the airway are a different complaint. Use clean nitrile, not bakery powder.",
            ],
        ),
        ext(
            "Third-party 510 sleeves and magnets",
            "Magnetic sleeves that look premium can lift a cart off the pin by half a millimetre. Then the hit is intermittent and everyone blames the board.",
            "Sleeve tests",
            [
                "Test the sleeve on the golden pair. If the LED strobes, the sleeve is a product, not an accessory afterthought.",
                "Retail will still sell the sleeve. Either qualify it or forbid it in writing on the insert.",
            ],
            "Silicone skins",
            [
                "Skins trap heat and hide swelling. They also hide oil. Ban skins on warehouse test units.",
                "If a brand wants a skin in retail, add a heat and leak test with the skin on. Otherwise you tested a different device.",
            ],
        ),
    ]),
    "g_fill": "\n".join([
        ext(
            "Night shifts and cap clocks",
            "Night shifts run fewer spot checks. That is when cap clocks drift from 90 seconds to 'whenever the tray looks full'.",
            "Audit hours",
            [
                "Audit at 02:00 once. You will learn more than from a daytime tour.",
                "If the timer is behind a laptop lid, it does not exist. Mount it on the capper.",
            ],
            "Music and speed",
            [
                "Loud music correlates with missed aim. This is not a culture war. Lower the volume on the fill line.",
                "If people need music, earbuds on one ear. Two ears and a chimney is how you paint mouthpieces.",
            ],
        ),
        ext(
            "Cleaning the filler between oils",
            "A live-resin run after distillate with the same needle is a blend you did not label.",
            "Flush rules",
            [
                "Flush volume should be written. 'Until it looks clean' is not a volume.",
                "Keep a reject jar for flush oil so it cannot wander into a sample tray.",
            ],
            "Allergens and botanicals",
            [
                "Botanical cuts can include allergens some markets care about. Residual in a needle is a labelling problem, not only a flavour problem.",
                "If you cannot flush, dedicate a filler. Dedicated equipment is cheaper than a recall letter.",
            ],
        ),
    ]),
    "g_volt": "\n".join([
        ext(
            "LED colours that lie about voltage",
            "Some pens use green/blue/red for steps. Staff memorise the wrong legend after a SKU mix.",
            "Legend on the bench",
            [
                "Print the LED legend next to the golden battery. Do not rely on a PDF in email.",
                "If two pens in the kit use opposite colour languages, you do not have a kit. You have a fight.",
            ],
            "Colour-blind staff",
            [
                "Do not make voltage depend only on colour. Shape, click count, or a printed icon has to exist too.",
                "This is not optional niceness. It is how you stop a red/green mix-up on a live-resin cart.",
            ],
        ),
        ext(
            "Customer 'secret settings' on forums",
            "Forums will tell people to click five times for hidden turbo. If your board has no such mode, say so on the insert.",
            "Hidden modes",
            [
                "If you do have a factory debug mode, disable it on retail firmware. Debug modes become clog clearers.",
                "If you cannot disable it, do not ship that board with live resin.",
            ],
            "Returns that cite a forum",
            [
                "Ask which setting they used. If they used a fantasy turbo, that is abuse, not a core defect.",
                "Still be polite. Then log it so product can decide whether the board is too easy to mythologise.",
            ],
        ),
    ]),
    "n_cer": "\n".join([
        ext(
            "Trade-show lighting versus warehouse lighting",
            "Booth LEDs hide scorch colour in oil. Warehouse tubes do not. Judge leftover oil under the light you actually pack in.",
            "Bring a lamp",
            [
                "A small 5000 K lamp in the QC corner is a tool. Phone torches lie warm.",
                "Photograph under that lamp so supplier arguments use the same colour temperature.",
            ],
            "Booth oils",
            [
                "Booth oils are often thinner distillates chosen to hit well. Your live resin will not.",
                "If they will not fill your oil at the booth, book a follow-up tray. The booth is advertising.",
            ],
        ),
        ext(
            "Second-source ceramic without a second SOP",
            "A second source is only a second source if the fill SOP stays the same and still passes.",
            "Qualification",
            [
                "Run the same n and the same leak check. Do not 'feel' that the new cart is close.",
                "If the second source needs a new cap clock, it is a new SKU. Give it a new code.",
            ],
            "Mixed cartons",
            [
                "Never mix two sources in one shipper to 'blend risk'. You will not know which source leaked.",
                "Mixed cartons turn a 2 percent problem into an un-traceable 100 percent argument.",
            ],
        ),
    ]),
    "n_cr": "\n".join([
        ext(
            "Airport security trays and CR packs",
            "Travellers will open CR packs in public. If the pack cannot be reclosed, your CR story ends at security.",
            "Reclosable or not",
            [
                "Know whether your market wants reclosable. If it does not, say so on a tuck-in so adults are not fighting a pouch on a plane.",
                "If it does, test reclose after a messy adult open, not only after a lab technician open.",
            ],
            "Lost pieces",
            [
                "CR tins with separate lids get separated. A tin without a lid is not CR. Tether or oversize the lid so it is obvious.",
                "If the lid is a tight disc that looks like a coaster, it will become a coaster.",
            ],
        ),
        ext(
            "E-commerce unboxing videos",
            "Influencers will show how to defeat your lock in 20 seconds. You cannot stop that. You can avoid a lock that is a puzzle box.",
            "Puzzle boxes",
            [
                "If the open sequence needs a diagram with six arrows, adults will use scissors. Scissors defeat CR and create sharp edges.",
                "Two motions max unless a lab already passed a more complex pack you cannot change.",
            ],
            "Comments",
            [
                "Do not argue with commenters about children. Point to the certified pack and stop. Comment wars are not a control.",
                "If the video shows a lock shipped off, that is your warehouse, not the influencer.",
            ],
        ),
    ]),
    "n_laser": "\n".join([
        ext(
            "Oil mist in the laser room",
            "If the laser sits next to the filler, mist will sit on lenses and on freshly marked parts. Separate the rooms.",
            "HVAC",
            [
                "Positive pressure on the laser side, or at least a door. Shared air is how you get cloudy marks and sticky fixtures.",
                "Wipe fixtures on a count. A sticky nest rotates the next unit.",
            ],
            "Fume",
            [
                "Polymer fume is a worker issue. Extract it. Do not 'open a window' as an SOP.",
                "If you laser coated metals, know the coating. Some coatings are not a hobby project.",
            ],
        ),
        ext(
            "Serial numbers that collide",
            "Lasered serials that repeat across two factories will ruin a recall. Allocate ranges in writing.",
            "Ranges",
            [
                "Factory A gets 1-499999. Factory B gets 500000 up. Do not be clever with dates in the serial if two lines can share a date.",
                "Print the range owner in the quality manual, not in a chat.",
            ],
            "Readable fonts",
            [
                "Stencil fonts survive small sizes better than script logos. Your wordmark can be pretty. The serial should be ugly and clear.",
                "If a camera cannot read it, your warehouse cannot either.",
            ],
        ),
    ]),
    "n_30": "\n".join([
        ext(
            "Rosin that arrives too cold",
            "Rosin stored near freezing can look like it needs max voltage. It needs time to come to fill temperature, not a turbo click.",
            "Temper",
            [
                "Temper oil to the SOP before you judge a 3.0 core. Cold oil plus low voltage looks like a dead heater.",
                "Then people crank the pen and scorch a core that was fine.",
            ],
            "Jars versus carts",
            [
                "A cold jar still smells loud. A cold cart does not hit. Staff will say 3.0 is weak. It is cold.",
                "Write temper time on the fill SOP in minutes, not 'until it looks runny'.",
            ],
        ),
        ext(
            "Mixing 3.0 and older cores in one display",
            "A display that mixes generations will train customers on the hotter one. Then the 3.0 SKU feels 'off'.",
            "Shelf talkers",
            [
                "If you must mix, the talker has to say start low on the live SKU. If you cannot fit that, do not mix.",
                "Staff will demonstrate the loudest device. That is the hotter one. Plan for that behaviour.",
            ],
            "Pricing",
            [
                "If 3.0 is priced like distillate hardware, purchasing will swap back the first time a spreadsheet blinks.",
                "Price it as a flavour SKU or do not bother with the generation story.",
            ],
        ),
    ]),
    "n_uk": "\n".join([
        ext(
            "Personal parcels and 'samples for a friend'",
            "Staff mailing filled samples to a friend in another country is an import. It is not a vibe.",
            "Policy",
            [
                "Written policy: no filled units in personal post. Empty hardware still has a value you must declare.",
                "If someone already posted it, tell counsel. Do not ask a blog what to do next.",
            ],
            "Shows",
            [
                "Hand-carrying hardware to a show is still movement of goods. Your ATA Carnet or equivalent is not this article's job, but pretending it is a laptop bag is how you fund a long afternoon at a desk.",
                "Batteries in carry-on have airline rules. Brief the person flying.",
            ],
        ),
        ext(
            "Website banners and market promises",
            "A banner that says ships everywhere is a legal problem on a hardware site that cannot ship everywhere.",
            "Coming soon",
            [
                "A coming-soon gate is not a licence. It only hides the site from casual visitors. It does not rewrite product law.",
                "Do not turn the gate off until claims and markets are aligned. That is an owner decision.",
            ],
            "Quotes",
            [
                "Quote emails should repeat destination country. If the buyer changes country in thread six, re-quote.",
                "Silent country changes are how you ship the wrong CR pack.",
            ],
        ),
    ]),
    "b_terp": "\n".join([
        ext(
            "Cleaning agents that smell like citrus",
            "Limonene-heavy cleaners in the same room as a flavour test will fool every nose on the shift.",
            "Room rules",
            [
                "No citrus cleaner on flavour-test day. Use unscented on that bench.",
                "If the room always smells of cleaner, you cannot QC aroma. Move the test.",
            ],
            "Hands",
            [
                "Scented lotion on an operator is a flavour contaminant. It sounds fussy. It is cheaper than a false fail on a 3.0 tray.",
                "Nitrile on the flavour station. Bare hands after lunch tacos is a known failure in more than one filling room.",
            ],
        ),
        ext(
            "Botanical terpene drums in heat",
            "Drums stored in a metal shed in July are not the same drums you QC'd in March.",
            "Drum logs",
            [
                "Log shed temperature. If it spiked, re-smell and, if you can, re-test the drum before you cut a production oil.",
                "A 'same drum' story with a heat spike is a new input.",
            ],
            "Headspace in drums",
            [
                "Partly empty drums oxidise faster. Do not keep a 20 litre drum at 1 litre for six months and call it consistent.",
                "Decant to smaller vessels if you must hold. Oxygen is a process aid you did not want.",
            ],
        ),
    ]),
    "b_vs": "\n".join([
        ext(
            "Cotton dust in a ceramic room",
            "If you still prototype cotton carts in the same room, fibre dust will find ceramic chimneys. Separate the benches.",
            "Housekeeping",
            [
                "Vacuum, do not blow. Blowing puts cotton into the next ceramic lot.",
                "If you stopped cotton last year and still find fibre in rejects, look at the ceiling vents.",
            ],
            "Staff loyalty to cotton",
            [
                "Some operators learned on cotton and will over-wet ceramic 'to be safe'. Retrain with the chimney photo, not a speech.",
                "Over-wet ceramic is a flood. Show them the mouthpiece. Speeches bounce.",
            ],
        ),
        ext(
            "Supplier dual-sourcing with mixed heaters",
            "A factory that runs cotton on line 1 and ceramic on line 2 will mix parts if totes look alike.",
            "Tote colour",
            [
                "Different tote colours. Different shelf heights. Physical controls beat emails.",
                "If a cotton heater lands in a ceramic body, your cutaway check should catch it. If you skipped cutaways, you will catch it in tickets.",
            ],
            "Incoming saw",
            [
                "Sacrificial cutaways on a sample of each lot are cheap. Keep the photos.",
                "If purchasing forbids cutting samples, they forbade QC. Escalate.",
            ],
        ),
    ]),
    "b_leak": "\n".join([
        ext(
            "Insurance photos versus process photos",
            "Insurance wants a wide shot of a stained box. Process wants a crop of the pin. Take both.",
            "Two cameras",
            [
                "A phone wide shot and a phone macro. Ten extra seconds.",
                "If the shop only sends the wide shot, ask for the crop before you change a core.",
            ],
            "Stained boxes",
            [
                "A stained shipper can be one unit. Do not condemn 200 from cardboard.",
                "Open, isolate, count. Cardboard is a sponge, not a census.",
            ],
        ),
        ext(
            "Staff using leakers as 'testers'",
            "A weeper used as a tester in the shop will weep on the counter all afternoon. Destroy it or quarantine it.",
            "Tester policy",
            [
                "Testers should be passed units, labelled, and replaced on a schedule. Leakers are not testers.",
                "If oil is on the counter, you are advertising a leak to every customer who leans in.",
            ],
            "Discount bins",
            [
                "Do not discount leakers at the counter. You train the market that your brand is the messy one.",
                "Destroy. Log. Move on. Discounting a leak is a brand decision you cannot un-tweet.",
            ],
        ),
    ]),
    "b_tray": "\n".join([
        ext(
            "Visitors who want to take a unit home",
            "A visitor pocketing an unmarked tray-one unit is a lost data point and sometimes a filled oil walking into an unlicensed space.",
            "Sign-out",
            [
                "If units leave the building, they are signed out empty or not at all. Filled tray-one units stay in the cage.",
                "If a founder takes one anyway, write it as lost. Do not pretend the n is still 80.",
            ],
            "Cages",
            [
                "A lockable cabinet is not drama. It is how you keep n honest.",
                "Label the cabinet with the tray number and date. Mystery cabinets become snack drawers.",
            ],
        ),
        ext(
            "Spreadsheet tabs that multiply",
            "One tab per tray. If someone makes a dashboard with seven blends, they will type the wrong leak rate into a board pack.",
            "Source of truth",
            [
                "The card on the tray is the source. The sheet is a copy. If they disagree, believe the card and photograph it.",
                "Dashboards are for passed trays, not for tray one still in process.",
            ],
            "Versioning",
            [
                "Filename with date and tray ID. 'final_final_tray' is how you quote last month's fail as a pass.",
                "When in doubt, reprint the card from the photo.",
            ],
        ),
    ]),
    "b_med": "\n".join([
        ext(
            "Supplier tours that skip the kiln",
            "A tour that shows the lobby and the packing table has not shown you the heater. Ask to see where ceramic is made or named, even if it is a partner site.",
            "Partner sites",
            [
                "If they will not name the ceramic source, you cannot file a materials story. That is the whole point of this essay.",
                "A brand-only office in a capital city is not a factory. Buy from the factory path.",
            ],
            "Gifts",
            [
                "A gift bag of marked pens is not a materials pack. Ask for the PDF with lot dates instead of the tote.",
                "If the PDF is older than the lot on the dock, it is a souvenir.",
            ],
        ),
        ext(
            "Internal Slack emojis as a quality system",
            "A thumbs-up on a photo of a cart is not incoming inspection. Emoji do not have lot codes.",
            "Tickets",
            [
                "Quality tickets need fields: lot, path, photo, operator. Slack is for pinging the person who will fill the ticket.",
                "If your only record is a thread, you will lose it when someone leaves.",
            ],
            "Language",
            [
                "Stop calling untested units 'medical' in Slack as a joke. Jokes leak into customer email.",
                "If you need a joke, joke about the coffee. Not about lungs.",
            ],
        ),
    ]),
}


def _notes(heading: str, pairs: list[tuple[str, str, str]]) -> str:
    bits = [h2(heading)]
    for h, a, b in pairs:
        bits.extend([h3(h), p(a), p(b)])
    return "\n".join(bits)


BOOST = {
    "g_oil": _notes("More oil-map habits that survive a busy week", [
        ("Monday oil versus Friday oil", "Friday oil is often warmer because the bath has been on all day. Treat that as a different fill even when the lot is the same.", "If Friday leak rates rise, check bath temperature before you blame the cart."),
        ("Shared pumps", "A pump that served distillate at noon and live resin at 15:00 is a blender. Flush or dedicate.", "Write the flush on the wall. Pumps do not remember."),
        ("Label printers", "If the oil map is only in a slide, nobody at the filler will open it. Print A3 and tape it up.", "Replace the print when the lot changes. Yellow tape over the old lot number is allowed. Ignoring the change is not."),
        ("Agency photos", "Agencies will fill a tank to the brim for a hero shot. Tell them no. Give them an empty dummy.", "A brim photo on the website trains your own staff to brim-fill. Kill it."),
        ("Seasonal cuts", "Summer live resin can run thinner. Winter distillate can sit. Recheck the map each season even if the SKU name is immortal.", "Immortal names are how hardware dies quietly."),
    ]),
    "g_charge": _notes("More charge-strip habits", [
        ("Loaner bricks", "Visitors will plug a random GaN charger into your strip because it is shiny. Confiscate it until they leave.", "Shiny is not qualified."),
        ("Tape residue", "Tape on USB-C heads collects oil. Replace marked cables when the tape is black, not when the cable dies.", "Black tape is a contamination flag."),
        ("Stacking packs", "Do not charge packs in a pile. Air has to move. A pile is a heater.", "One layer on a metal shelf. That is the whole SOP."),
        ("End of day", "Unplug the strip at close even if a pack is at 80 percent. A building with no people should not babysit lithium.", "Timers help. Habits help more."),
        ("New SKUs", "A new battery SKU needs a new legend on the wall the same day it arrives. Yesterday's blink code will be applied to it by default.", "Default is how you cook a cell."),
    ]),
    "g_510": _notes("More 510 bench habits", [
        ("Thread gauges", "A cheap thread gauge pays for itself the first time you catch a bad lot at receiving.", "If purchasing will not buy a gauge, they bought tickets instead."),
        ("Oil as lube", "Operators will 'ease' a tight 510 with oil. That paints the pin on purpose. Ban it.", "If it will not start by hand, it is a defect or a cross-thread. Not a lube job."),
        ("Bin of mystery pens", "The drawer of old batteries is not a test rack. Label a test rack with one SKU.", "Mystery pens create mystery fails."),
        ("Drop tests", "A 510 cart that survives a 1 m drop onto wood may still fail onto tile. Test the floor you actually have.", "Then decide if retail packaging needs a harder insert."),
        ("Customer mix", "If customers will use box mods, say you do not support that on the insert. 510 fits. Voltage may not.", "Fit is not endorsement."),
    ]),
    "g_fill": _notes("More fill-line habits", [
        ("Needle inventory", "Needles disappear into pockets. Count them like blades. A missing needle is a product-safety event if it is in a tank.", "Metal detectors are not a joke in some plants. Use them if you have them."),
        ("Floor spills", "Oil on the floor is a slip and a lot-control mess. Treat spills as a quality hold until you know which lot hit the floor.", "Mop last. Bag first."),
        ("Temp guns", "A cheap IR gun on the oil bath is better than a hand test. Hands lie when they are cold from the loading dock.", "Log the number. Do not log 'felt fine'."),
        ("Open windows", "Pollen and dust love open tanks. If you need air, filter it. Windows are not a cleanroom.", "Summer fill weeks show this. Winter staff forget until June."),
        ("Piecework pay", "If you pay by unit filled, you paid for leaks. Pay for passed 24-hour checks or accept the leak rate.", "Incentives are a fill spec."),
    ]),
    "g_volt": _notes("More voltage-control habits", [
        ("QR codes", "A QR on the box that opens the voltage note is better than a paragraph nobody reads. Keep the landing page stable.", "If the QR dies, the insert still needs the start-low line."),
        ("Influencer kits", "Send influencers the same insert. They will still max it. Ask them to film the start-low step or do not send live resin.", "A scorched review is a voltage review."),
        ("POS notes", "If the till can print a one-liner, print start low. Till paper outlasts posters.", "Out of paper is not an excuse to skip it on a live SKU."),
        ("Returned pens", "A returned pen stuck on the top step is evidence. Photograph the LED before you reset it.", "Resetting first is how you lose the plot."),
        ("Kit mix-ups", "A distillate kit battery in a live-resin box happens in packing. Barcode the battery, not only the outer carton.", "Packing is a voltage setting."),
    ]),
    "n_cer": _notes("More ceramic-buying habits", [
        ("Samples that arrive filled", "Filled samples you did not fill are a different oil. Empty them in your notes even if you still smell them.", "Smell is not a leak test."),
        ("MOQ traps", "A low MOQ with a high colour MOQ is how you get stuck with 5000 blue units of a core you have not passed.", "Pass first. Colour second. MOQ third."),
        ("Broker English", "If the only English speaker is in sales, engineering questions will die. Demand a process contact.", "Sales cannot name a pore size."),
        ("Price lists without lots", "A price list is not a quality agreement. Add lot trace as a line, not a hope.", "Hope is not incoming inspection."),
        ("Show specials", "Show-only pricing often matches show-only lots. Ask whether the lot you saw is the lot you will receive in October.", "October is when the tickets arrive."),
    ]),
    "n_cr": _notes("More child-resistant operating notes", [
        ("Gloves and CR", "Winter gloves make two-motion opens fail for adults. Test with gloves if your market is cold.", "A lock that only works bare-handed will generate January tickets."),
        ("Arthritis", "If your buyers skew older, watch a real older adult open the pack. Labs include older adults for a reason.", "Your nephew is not a panel."),
        ("Warehouse knives", "Staff who slice CR pouches to speed picking have destroyed the control. Ban blades on those SKUs or accept the audit story.", "Speed is not a defence."),
        ("Returns without lids", "A returned tin with no lid is not restockable as CR. Scrap it.", "Restocking it is how you fail the next mystery shop."),
        ("Gift sets", "Gift sets that put a CR pouch inside a magnetic box add a layer children may not even see. Test the outer box too if that is what they encounter first.", "Layers only count if they are the first layer."),
    ]),
    "n_laser": _notes("More laser-room operating notes", [
        ("Job tickets", "A laser job without a body SKU on the ticket will mark the wrong colour. The contrast will be wrong and nobody will know until packing.", "SKU on the ticket. Always."),
        ("Night operators", "Night laser operators skip fixture checks. Put a checklist on the machine, not in a drawer.", "Drawers are where checklists go to die."),
        ("Customer late art", "Late art is how you skip the oil-wipe test. Then the mark dies in week two.", "If art is late, the ship date moves. Do not move the test."),
        ("Scrap marked units", "Scrapped marked units in a bin are a counterfeit risk if they leave the building. Destroy the mark or crush the body.", "Bins get photographed."),
        ("Font licensing", "A pretty font on a laser that you do not license is a legal mess on 20,000 units. Use a licensed or owned cut.", "Pretty is not a licence."),
    ]),
    "n_30": _notes("More 3.0 sampling notes", [
        ("Jar temperature", "Compare jar and cart at the same temperature. A warm cart versus a cold jar is a dishonest test.", "Honesty is a thermometer."),
        ("Draw length", "Staff who take two-second draws will say 3.0 is weak. Time the draw in the protocol.", "Un-timed draws are opinions."),
        ("Cleaning between samples", "A hot-core control that is not cleaned will contaminate the 3.0 session with old oil smell.", "Clean or you did not blind anything."),
        ("Batch stories", "If 3.0 passes on lot A and fails on lot B, believe the oil change. Do not quietly swap the heater and forget to tell brand.", "Tell brand. They own the jar card."),
        ("Spare boards", "A 3.0 all-in-one with a noisy board is a board issue. Do not condemn the generation on one loud unit.", "n=1 is a story. n=30 is a test."),
    ]),
    "n_uk": _notes("More UK and export operating notes", [
        ("Personal LinkedIn posts", "Staff posting 'we ship worldwide' on LinkedIn is a claim. Brief them. Delete if needed.", "Personal brands are still your brand."),
        ("Show catalogues", "A catalogue that lists every country as a flag is a claim. Use 'enquire' until counsel agrees.", "Flags are not licences."),
        ("Sample invoices", "Zero-value invoices for lithium and hardware annoy customs. Put a true value.", "True value is boring and correct."),
        ("Returns from abroad", "A filled return crossing a border is still filled. Do not call it empty to make the paperwork pretty.", "Pretty paperwork is a later problem."),
        ("Counsel retainers", "If you cannot name your counsel, you are not ready to turn coming soon off for every market.", "Hardware can be ready. The file might not."),
    ]),
    "b_terp": _notes("More terpene-lab habits", [
        ("Open beakers", "Open beakers of botanical cut next to the flavour station will drown every cart in lemon.", "Lids. Always lids."),
        ("Fridge odours", "A fridge that also holds lunch will flavour the oil. Dedicated fridge or stop pretending the test is clean.", "Onion is not a terpene you wanted."),
        ("Paper cups", "Tasting from a paper cup that held coffee is a ruined session. Use glass that was washed with unscented soap.", "Coffee is stubborn."),
        ("New staff noses", "New staff with a cold should not flavour-pass. Send them home or send them to leak photos.", "Blocked noses pass burnt oil."),
        ("Chart printouts", "A boiling-point chart on the wall should say 'not a setpoint' in 24 pt type.", "Someone will still set a pen to 176. The sign is for them."),
    ]),
    "b_vs": _notes("More ceramic-versus-cotton operating notes", [
        ("Tote lids", "Cotton parts in open totes shed. Lids on totes. It is that small and that ignored.", "Ignored small things become mixed heaters."),
        ("Training videos", "A training video shot on cotton will be applied to ceramic for years if you do not retire it.", "Retire the video the week you retire the wick."),
        ("Spare parts drawers", "A drawer labelled heaters that holds both types is a trap. Split drawers physically.", "Labels peel. Walls do not."),
        ("Cost-down workshops", "Workshops that start with 'take 10 percent out of the cart' will land on cotton. Start with leftover oil instead.", "The agenda is the outcome."),
        ("Customer repairs", "Customers who 're-wick' a ceramic cart with cotton from a pouch are not your SOP. Do not bless it on social.", "Blessing it makes it your SOP."),
        ("Line clearance", "Clear the cotton line completely before a ceramic run. A single leftover coil is a lot fail.", "Walk the line. Do not ask if it was cleared."),
        ("Incoming smell", "Cotton lots can smell like warehouse. Ceramic should not smell like a wick. Smell incoming. It is free.", "Free tests still count."),
        ("Board decks", "If the board packet has no leftover-oil chart, add one or cancel the meeting.", "Meetings without numbers pick cotton."),
    ]),
    "b_leak": _notes("More leak-cost operating notes", [
        ("Counter paper", "White paper on the tester tray shows new oil in minutes. Brown wood shows it in a day, which is too late.", "Paper is a sensor."),
        ("Staff hoodies", "Oil on a hoodie is a walking display of a leak. Issue aprons on the fill and test benches.", "Fashion is not PPE."),
        ("Carrier claims", "Carriers will blame 'improper packing' for every stain. Your insert photos from before ship are the rebuttal.", "No photos, no rebuttal."),
        ("Batch holds", "Hold the remainder of a lot when leak photos cluster. Shipping the rest 'because they are already boxed' is how you fund the cluster.", "Boxed is not passed."),
        ("Customer videos", "A slow-motion leak video is better data than an angry paragraph. Ask for it. Save it.", "Anger without a path is not actionable."),
        ("Discount math", "A 30 percent off leak sale still trains the market. Destroy instead.", "Training the market is the expensive part."),
        ("Night porters", "Cleaners who wipe oil and say nothing hide rates. Ask them. Pay them to report.", "Hidden rates look like quality."),
        ("Spare gaskets", "If you can re-gasket in the field, you already admitted the gasket was the cheap part. Do it in the factory instead.", "Field repairs are a confession."),
    ]),
    "b_tray": _notes("More sample-tray operating notes", [
        ("Guest badges", "Guests on the tray floor sign a one-pager: no pockets, no filled souvenirs. It feels corporate. It keeps n honest.", "Honesty has a badge."),
        ("Holiday weeks", "Trays started on a Thursday before a holiday will be forgotten on a warm shelf. Do not start them.", "Start on a week you will be present for the 24-hour check."),
        ("Shared fridges", "A tray in the staff fridge will be moved by someone looking for milk. Dedicated shelf or no fridge.", "Milk always wins."),
        ("Duplicate cards", "Two cards in one tray is how the wrong voltage gets written. One card, one clip, one owner.", "Owners can go on holiday. Then the deputy owns the clip."),
        ("Photo lighting", "Photograph leaks in the same corner every time. Mixed lighting starts arguments about 'how wet'.", "Same corner. Same lamp."),
        ("Vendor visits", "Vendors who rearrange the tray to look nicer have voided it. Tell them no before they arrive.", "Pretty trays are not data."),
        ("Scale calibration", "A scale that has not been checked in a year is a storyteller. Check it the morning of tray one.", "Stories are not masses."),
        ("Stop rules", "Write the stop rule on the card: three weepers and we stop. If you decide later, you will not stop.", "Later is how 200 bad units get filled."),
    ]),
    "b_med": _notes("More materials-file operating notes", [
        ("PDF dates", "A certificate older than the lot is a souvenir. Ask for the matching lot or write 'unverified' on the RFQ score.", "Unverified is allowed. Pretend is not."),
        ("Translator upgrades", "If a translator writes hospital, change it back before print. Log the change so it does not return in the next language.", "Languages regress to hype."),
        ("Sales adjectives", "Sales will say medical-grade on calls. Give them a banned-word list. Role-play once. It is awkward and it works.", "Awkward is cheaper than a claim letter."),
        ("Pellet versus part", "A resin pellet certificate is not a mouthpiece certificate. Ask which part was moulded from which lot.", "Pellets are not mouthpieces."),
        ("Oil soak logs", "If you soak parts in your oil for 72 hours, log swell and taste. That log is more useful than a lobby trophy.", "Trophies do not swell."),
        ("Visitor NDAs", "NDAs do not replace a materials list. You can NDA a drawing and still name the polymer family.", "Secrecy about the family is a red flag."),
        ("Website footers", "Footers that say not a medicine help. Packs that say inhaler undo the footer. Align them.", "Footers are not a spell."),
        ("Internal jokes", "Jokes about lungs in the factory group chat will be screenshotted. Assume they will be.", "Assume the screenshot."),
    ]),
}


BOOST2 = {
    "g_510": _notes("510 edge cases from filling partners", [
        ("Left-handed operators", "Left-handed capping on a right-handed fixture increases cross-threads. Offer a mirrored fixture or train the start angle.", "Start angle is a posted photo, not a speech."),
        ("Cold warehouses", "Metal contracts in the cold. A 510 that is snug at 20 C can feel loose at 4 C. Test both if you store cold.", "Loose in the cold is not a different thread. It is physics."),
        ("Retail screwdrivers", "Some shops use a driver on 510 carts. Ban it on the insert. Drivers lift pins.", "If they keep doing it, your pin spec needs more margin or you need a different retail channel."),
        ("Counterfeit threads", "Off-market 510 carts with ugly threads will be used on your battery. Your insert should say tested on Justccell batteries, not on every stick on earth.", "Every stick on earth is a lie."),
        ("Washers", "People add tiny washers to 'improve contact'. They create leaks at the shoulder. Ban washers.", "If contact is bad, replace the cart or battery. Do not add jewellery."),
        ("Serial mixing", "Two 510 families in one bag look the same when oily. Bag by SKU, not by '510'.", "Oily bags are how TH2 and M6T swap."),
        ("Customer 510 cleaners", "Wire brushes on pins flatten them. Recommend a cloth only.", "A flattened pin is a new intermittent."),
        ("End-of-line spin", "A final spin test that overtightens to hear a click is a crack generator. Stop at seated.", "Clicks are not a spec."),
    ]),
    "g_fill": _notes("Fill edge cases from filling partners", [
        ("Foam from nitrogen", "If you blank with nitrogen and get foam, you overdid the gas. Foam is not a fill line.", "Wait, then weigh."),
        ("Dark oils", "Dark oils hide the chimney. Light the tank from behind or rely on mass only.", "Eyes lose. Mass wins."),
        ("Sticky floors", "Sticky floors slow cappers. Then clocks blow. Clean the floor as a quality task, not a janitor afterthought.", "The clock cares about shoes."),
        ("Shared scales", "A scale that also weighs incoming freight will be out of calibration for milligrams. Dedicated fill scale.", "Freight scales are not fill scales."),
        ("Temp probes in oil", "A dirty probe is a flavour defect. Wipe it. Assign it. Do not stir with a screwdriver.", "Screwdrivers are not probes."),
        ("Open totes of mouthpieces", "Dusty mouthpieces become dusty airways. Keep bags closed.", "Closed bags are free QC."),
        ("Piece counts", "If the filler and capper counts disagree, you have open tanks somewhere. Find them before lunch.", "Open tanks over lunch are leaks."),
        ("New hires on Fridays", "Do not put new hires on fill on Friday afternoon. Put them on labels.", "Friday fill is how you buy Monday photos."),
    ]),
    "g_volt": _notes("Voltage edge cases from filling partners", [
        ("Two-button combos", "If a pen needs a combo to change volts, print the combo as icons. Text will be ignored.", "Icons survive languages."),
        ("Dead LED", "A dead LED makes staff guess the step. Guessing is max. Fail the pen.", "A flavour test on a guessed step is void."),
        ("Shared demo battery", "One demo battery on max all day will scorch every cart you demonstrate. Reset to low between SKUs.", "Demos are a voltage SOP."),
        ("Child lock plus volts", "If CR lock and voltage share a button, staff will confuse them. Separate the motions or the training will fail.", "Failed training is max voltage."),
        ("Firmware twins", "Two pens that look identical with different firmware will have different steps. Colour the shell or the SKU will lie.", "Looks are not firmware."),
        ("Car demos", "12 V car chargers are not 5 V bricks and not voltage steps. Do not demo in a car.", "Cars are heat plus confusion."),
        ("Returned screenshots", "Ask for a photo of the LED. Without it, every ticket is 'it was too hot'.", "Photos split abuse from defects."),
        ("Kit inserts in the wrong language", "A Spanish insert on a UK distillate kit will still be read for the number. Keep the number correct even if the rest is wrong.", "Numbers travel."),
    ]),
    "n_cr": _notes("CR edge cases from brand teams", [
        ("Seasonal sleeves", "A Christmas sleeve over a CR pouch can hide the opening. Test the sleeved pack.", "Sleeves are part of the pack."),
        ("Influencer unbox jigs", "If you send a jig that defeats CR for filming, that jig will leak online. Do not send it.", "Film with the real open."),
        ("Warehouse heat", "Heat makes some pouches gummy. Gummy is not the lab condition. Test a hot pouch.", "Labs are 23 C. Vans are not."),
        ("Dual language instructions", "CR instructions in 6pt bilingual type will not be read. Use diagrams.", "Diagrams beat 6pt."),
        ("Subscription boxes", "A subscription outer that is easy to open plus a CR inner is fine if the inner is first-open for the child. If the inner is a sticker, it is not.", "Stickers are not CR."),
        ("Staff samples", "Staff who take unlocked samples home have a different product. Log samples as unlocked and non-retail.", "Unlocked is a different SKU."),
        ("Knife slit QC", "QC that slits packs to inspect will not restock them. Mark them destroyed.", "Slit is destroyed."),
        ("Retailer overrides", "A chain that demands easy-open for 'accessibility' may be asking you to drop CR. Get that in writing and send it to counsel.", "Do not silent-drop CR."),
    ]),
    "n_laser": _notes("Laser edge cases from brand teams", [
        ("Secret marks", "A tiny unmarked identifier for grey-market tracing is fine. A tiny health claim is not.", "Keep secrets in codes, not in sentences."),
        ("Curved surfaces", "Lasers on curves walk. Fixture the curve or accept a drunk wordmark.", "Drunk wordmarks get screenshotted."),
        ("Battery wraps", "Laser then wrap, or wrap then laser, is a process. Mixing them in one PO is two processes. Pay for two.", "Two processes, two approvals."),
        ("Customer on the floor", "Customers who 'just want to watch the laser' will bump fixtures. Viewing window or no visitors.", "Bumped fixtures are wrong logos."),
        ("Power cuts", "A mid-mark power cut leaves a half logo. Those units are scrap, not seconds.", "Seconds with half logos are counterfeit bait."),
        ("Oil on fixtures", "Oil on a nest slips the next unit. Wipe on a count.", "Counts are cheaper than reprints."),
        ("QR too small", "A QR that does not scan at arm length is decoration. Test with a cheap phone.", "Cheap phones are your customers."),
        ("Colour-change polymers", "Some polymers yellow after laser. Age a sample in light before you approve.", "Day-zero white is a lie."),
    ]),
    "n_30": _notes("3.0 edge cases from brand teams", [
        ("Double-blind fatigue", "Staff stop being blind after lunch. Run the important comparison in the morning.", "Morning noses are better."),
        ("Rosin clumps", "Clumpy rosin in a 3.0 tank is a fill issue. Warm and homogenise before you condemn the heater.", "Clumps are not pores."),
        ("Travel cases", "A 3.0 all-in-one in a hot car is a different device. Do not compare it to a desk sample.", "Cars are ovens."),
        ("Firmware on pods", "If a pod board can be updated to a hotter curve, lock it. Updates that 'improve vapour' will scorch live oil.", "Lock the curve."),
        ("Gift with purchase", "A free hot pen with a 3.0 cart is sabotage. Do not bundle opposites.", "Bundles are specs."),
        ("Lab versus floor", "A 3.0 pass in a quiet lab can fail on a loud floor where people hold the button. Test with real draw habits.", "Habits are the heater."),
        ("Spare mouthpieces", "Swapping mouthpieces mid-test changes airflow. Do not.", "Airflow is a variable."),
        ("Brand decks", "Decks that say 3.0 makes any oil legal are a fireable slide. Remove it.", "Slides get forwarded."),
    ]),
    "n_uk": _notes("UK and export edge cases", [
        ("Flag icons on SKUs", "A Union Jack on a cart that cannot be sold to UK consumers is a problem. Remove the flag or change the SKU.", "Flags are claims."),
        ("Show-only stickers", "Not for sale stickers that fall off are for sale. Use a mark that survives.", "Fall-off is for sale."),
        ("Shared inboxes", "A shared sales inbox will answer a legal question with a smiley. Route legal strings to a person.", "Smileys are not files."),
        ("Agent websites", "Distributors who rewrite your claims on their site are you in that market. Audit them.", "Their site is your claim."),
        ("Sample fairs", "Filled samples at a fair in a country you are not licensed in are a problem. Empty or stay home.", "Fairs are borders."),
        ("Translation memory", "Old TM that says ships worldwide will reappear. Purge it.", "TM is a zombie."),
        ("Insurance questions", "If insurance asks what you sell and you say vapes, add empty hardware for licensed fillers. Precision helps.", "Vague is expensive."),
        ("Domain copies", "A second domain with stronger claims will be read as you. Align or redirect.", "Domains talk."),
    ]),
    "b_terp": _notes("Terpene edge cases on the floor", [
        ("New perfume", "A visitor in strong perfume voids a flavour morning. Reschedule.", "Perfume is a contaminant."),
        ("Cardboard boxes", "New cardboard smells. Do not flavour-test next to a delivery of boxes.", "Cardboard is loud."),
        ("Sanitiser", "Alcohol sanitiser in the nose is a flavour defect. Flavour station is a no-sanitiser zone. Use gloves instead.", "Gloves over gel."),
        ("Strain names", "Staff will say the cart 'is not mango' because the name is mango and the terpenes are mixed. Score against the jar, not the name.", "Names are marketing."),
        ("Second hit bias", "The second hit is hotter. Protocol should say which hit you score.", "Unstated hits are arguments."),
        ("Water as palate", "Water is fine. Coffee is not. Mint gum is not.", "Mint gum is a weapon."),
        ("Jar lids left off", "A jar left open for an hour is not the control. Recap between testers.", "Open jars drift."),
        ("Spreadsheet stars", "Five-star flavour scores without a note are useless. Require a five-word note.", "Stars are not notes."),
    ]),
    "b_vs": _notes("Ceramic versus cotton edge cases", [
        ("Contractor crews", "Contractors who 'help' on a Saturday will use the wrong tote. Lock totes.", "Saturday is a quality day."),
        ("Free samples from cotton vendors", "Free cotton carts in the ceramic plant are a trap. Do not accept them on site.", "Free is fibre in the vents."),
        ("RMA tear-downs", "Tear-downs must record heater type with a photo. Memory will say ceramic when it was cotton.", "Photos or it did not happen."),
        ("Customer 'upgrades'", "Customers stuffing cotton into a ceramic chimney are not an upgrade. Do not coach it.", "Coaching makes it yours."),
        ("Price lists in EUR and GBP", "A cheaper cotton line in one currency will get ordered by accident. Different SKU roots.", "Roots stop accidents."),
        ("Intern projects", "Interns 'testing cotton one more time' need a written purpose or they are nostalgia.", "Nostalgia leaks."),
        ("Board observers", "If a director picks up a cotton cart because it looks familiar, hand them leftover-oil numbers, not a vibe.", "Vibes are cotton's friend."),
        ("End-of-life", "Destroy cotton inventory you retired. Selling it cheap later restarts tickets.", "Retirement means destroy or clearly grey-market with no brand."),
    ]),
    "b_leak": _notes("Leak edge cases on the floor", [
        ("Tape as a fix", "Tape on a mouthpiece is not a process. It is a confession on the shelf.", "No tape."),
        ("Customer rice tricks", "People putting carts in rice for leaks will send you rice. Ignore the rice. Ask for photos of the path.", "Rice is not data."),
        ("Freezer tests", "Freezing a cart then warming it is an abuse test. If you did not spec it, do not accept it as a defect.", "Abuse is allowed as a test you chose."),
        ("Shared testers", "A tester that many staff pocket-carry will leak from body heat and orientation. Testers stay on the tray.", "Pockets are sideways ovens."),
        ("Label glue", "Aggressive label glue can lift a mouthpiece seat. Test the label on the real geometry.", "Labels are hardware."),
        ("Shrink tunnels", "Heat tunnels for packs will thin oil. If you shrink filled goods, test that tunnel.", "Tunnels are fill temperature."),
        ("Returns without lot", "No lot, no autopsy. Train retail to photograph the lot before they bin the box.", "Binned lots are ghosts."),
        ("Team chat diagnosis", "Do not diagnose a leak from a blurry story in chat. Demand the two photos.", "Chat is not QC."),
    ]),
    "b_tray": _notes("Tray edge cases on the floor", [
        ("Two oils, one tray", "If someone sneaks a second oil onto tray one 'for efficiency', void the tray.", "Efficiency is confound."),
        ("Borrowing units", "Sales borrowing three units for a lunch demo voids n. Chain them or say no.", "Lunch demos are not tray one."),
        ("Sticky notes as cards", "Sticky notes fall off. Use a clipped card.", "The floor eats sticky notes."),
        ("Fridge power cuts", "A power cut overnight is a new temperature cycle. Log it or void.", "Unlogged cycles are mysteries."),
        ("Language mix", "A card in English and a filler who writes in another language will misread voltage. Use numbers and SKU codes.", "Codes survive language."),
        ("Vendor stickers", "Vendor stickers over your lot code are a fight. Peel them at receiving, not at autopsy.", "Receiving is the fight you can win."),
        ("Overtime trays", "A tray started in overtime with tired staff is a different process. Mark it as such.", "Tired is a variable."),
        ("Board dates", "Do not move a launch date into a tray still on the bench. Move the date.", "Dates do not pass leak tests."),
    ]),
    "b_med": _notes("Materials-file edge cases", [
        ("WhatsApp certificates", "A photo of a certificate in WhatsApp is not a file. Get the PDF with a hash or a portal download.", "WhatsApp compresses trust."),
        ("Stamped in red", "A red stamp does not make a pellet into a part. Read the scope line.", "Scope is the only line."),
        ("Consultant summaries", "A consultant slide that says medical-grade with no table is a vibe. Demand the table.", "Tables or no slide."),
        ("Old websites", "Supplier websites that still show cotton while the email says ceramic are a mixed factory. Ask which page is true.", "Both might be true. That is the problem."),
        ("Customs material codes", "HS codes are not a toxicology file. Do not wave them at a reviewer as if they were ISO.", "HS is trade, not lungs."),
        ("Influencer doctors", "A doctor on Instagram is not your QP. Do not quote them on a pack.", "Packs need files."),
        ("Internal wikis", "A wiki page titled medical that anyone can edit is not a record. Lock it or move it to a QMS.", "Wikis drift."),
        ("End user emails", "If an end user asks if it is a medicine, the reply is no unless it is. Save the template.", "Templates stop poetry."),
    ]),
}


BOOST3 = {
    "n_cr": _notes("CR warehouse close-out notes", [
        ("Count the locks", "A carton of all-in-ones should match lock-on counts. Spot-check ten. If two are off, hold the carton.", "Off is a process, not a unit."),
        ("Spare pouches", "Spare CR pouches without lot codes are useless in a recall. Print lots on spares too.", "Spares are product."),
        ("Night air", "Humid nights make paperboard CR cartons swell. If your test was in dry air, retest in humidity or accept January fails.", "Humidity is a spec."),
        ("Customer scissors again", "If tickets mention scissors twice in a week, the open is too hard for adults. Redesign. Do not tell adults to try harder.", "Harder is a fail."),
        ("Show demo defeat", "A show staffer who defeats CR as a joke will be filmed. Brief them like they are on camera. They are.", "Shows are public."),
        ("Box-in-box", "An easy outer plus CR inner only works if the inner is what a child gets first after the outer is trash. Map the first-open.", "Trash is part of the sequence."),
    ]),
    "n_laser": _notes("Laser warehouse close-out notes", [
        ("Ink versus laser", "If you pad-print and laser the same body, sequence them. Pad over laser can fill the etch. Laser over pad can smell.", "Pick a sequence and freeze it."),
        ("Operator initials", "Initials on the job ticket catch who skipped the wipe. Not to punish. To train.", "No initials, no training target."),
        ("Monday fixtures", "Monday fixtures are cold and tight. First ten units may sit high. Scrap or re-mark. Do not ship the Monday lean.", "Monday lean is a known ghost."),
        ("Customer rush fees", "Rush fees that skip the wipe test are a sale of future tickets. Charge more or say no.", "No is a quality tool."),
        ("Archive photos", "Photograph the first and last unit of a laser lot. That pair settles 'it always looked like that' arguments.", "Archives are cheap."),
        ("Sleeve orientation", "A sleeve with a front and a back needs a fixture key. If the key is 'the operator knows', they will not on night shift.", "Keys beat knowledge."),
    ]),
    "n_30": _notes("3.0 warehouse close-out notes", [
        ("Control cart labels", "Label the hot-core control in a colour you never sell. Mix-ups make 3.0 look like the old core.", "Ugly labels are a feature."),
        ("Oil arrival photos", "Photograph the oil jar on arrival. If later someone says the jar was already flat, you have a picture.", "Pictures beat memory."),
        ("Button hang", "A sticky button holds voltage. 3.0 plus a sticky button is a scorched cart. Fail the button, not the generation.", "Buttons are heaters too."),
        ("Staff preference", "Some staff prefer harsh vapour and will down-score 3.0. Score against the jar, not against their hobby.", "Hobby is bias."),
        ("Spare tanks", "Swapping tanks mid-ladder without noting it voids the ladder. Write every swap.", "Unwritten swaps are new tests."),
        ("Launch copy", "Launch copy that says never burns is a lie. Say less likely to scorch live oil at low volts. Stop.", "Never is a ticket."),
    ]),
    "n_uk": _notes("Export close-out notes", [
        ("Brochure dates", "A 2024 brochure on a 2026 stand will contain old claims. Date brochures or leave them home.", "Old paper still speaks."),
        ("Agent NDAs", "NDAs with agents who still overclaim are paper. Audit their storefronts quarterly.", "Quarterly is a calendar, not a vibe."),
        ("Sample stickers", "Sample not for resale must survive a wipe. If it wipes off, it was for resale.", "Wipes are tests."),
        ("Shared freight", "Sharing a pallet with a nicotine brand can confuse paperwork. Separate SKU lists on the invoice.", "Confusion is a delay."),
        ("Owner quotes", "Owner quotes on podcasts are claims. Brief the owner or do not do podcasts until coming soon is a chosen public state.", "Podcasts outlive coming soon."),
        ("Redirects", "A domain redirect from a louder brand claim still carries the claim in archives. Clean the source if you can.", "Archives remember."),
    ]),
    "b_terp": _notes("Terpene close-out notes", [
        ("Jar freeze-thaw", "A jar that froze in a van then thawed is a new oil. Do not use it as the aroma control.", "Freeze-thaw is a lot change."),
        ("Two testers", "Flavour pass needs two people when the SKU is live. One person will always 'kind of' pass it.", "Kind of is a fail."),
        ("Time of day", "Score flavour before lunch. After lunch is soup and doubt.", "Soup is bias."),
        ("Cart rest", "A cart that sat on a hot rail for an hour is not the same cart. Cool it or void the score.", "Rails are heaters."),
        ("Note templates", "Force a note: citrus / pine / flat / burnt / other. Other requires five words.", "Other without words is a shrug."),
        ("Retired charts", "Take down the vacuum-point caryophyllene chart. It is still teaching 130 C to someone.", "Walls teach."),
    ]),
    "b_vs": _notes("Ceramic versus cotton close-out notes", [
        ("Last cotton PO", "Write the last cotton PO number on the wall the day you retire it. Then people stop 'just ordering a few'.", "A few is a restart."),
        ("Spare cotton coils", "Spare coils in a personal toolbox will return at Christmas. Collect toolboxes once.", "Christmas coils are a lot fail."),
        ("Customer letters", "If a customer loved cotton, do not restart the line. Offer a ceramic sample on their oil.", "Love is not a spec."),
        ("Finance restock", "Finance restocking cotton because MOQ was cheaper is a silent strategy change. Make it loud or stop it.", "Silent strategy is cotton."),
        ("Photo archive", "Keep one cutaway photo of cotton and one of ceramic on the quality wall. New hires need to see the difference.", "Walls onboard."),
        ("Vendor lunch", "A cotton vendor lunch is not a trial protocol. If they want a trial, they fill your oil under your SOP.", "Lunch is not n=200."),
    ]),
    "b_leak": _notes("Leak close-out notes", [
        ("Last mile scooters", "Scooter bags lay carts on their side in heat. If that is your channel, test that bag.", "Channels are orientations."),
        ("Counter LEDs", "Hot LEDs over testers warm tanks. Move testers or move lights.", "Lights are fill temperature."),
        ("Unsigned RMAs", "An RMA without a photo is a story. Require the photo to open the ticket.", "No photo, no ticket."),
        ("Batch bingo", "Do not wait for a perfect pattern of ten. Three same-path weepers is a hold.", "Bingo is delay."),
        ("Customer rice again", "If rice appears twice, add a line on Discover: we need path photos, not rice.", "The hub can say that without a question list."),
        ("Destroy logs", "Log destroyed leakers. Unlogged destroys look like theft or like hidden rates.", "Logs keep rates honest."),
    ]),
    "b_tray": _notes("Tray close-out notes", [
        ("Last unit temptation", "The last unit always gets gifted. Tape it to the card instead.", "Gifts shrink n."),
        ("Rain on receiving", "Wet cartons of empty hardware are a rust and paper story. Dry before you fill.", "Rain is incoming inspection."),
        ("Two shifts, one tray", "If shift two continues shift one's tray, they must read the card aloud. Silent continuations skip the cap clock.", "Aloud is a control."),
        ("Vendor coffee", "Vendors who bring coffee onto the tray bench spill. Coffee is a flavour and a slip. Coffee stays off the bench.", "Benches are dry."),
        ("Launch party units", "Party units are not tray units. Pull them from a passed lot or do not serve them.", "Parties are not QC."),
        ("Sheet backups", "Email the tray photo to a shared inbox at 24 hours. Laptops die. Inboxes remember.", "Inboxes are backups."),
    ]),
    "b_med": _notes("Materials close-out notes", [
        ("Last-minute adjectives", "A homepage rewrite at 11pm will reinsert medical. Watch the homepage like a pack.", "Homepages leak claims."),
        ("Sales decks in Drive", "Old decks in Drive are still sent. Archive them or they are current.", "Drive is infinite."),
        ("Supplier rebrands", "A supplier who rebrands and keeps the same certificate PDF is a new company with an old souvenir.", "Ask for a new file."),
        ("Part versus system", "A medical-grade gasket in a mystery body is not a medical-grade device. Score the system.", "Systems leak, not adjectives."),
        ("Training slides", "Training that says hospital quality must be rebuilt. Training is a claim surface.", "Rebuild it."),
        ("Owner vocabulary", "If the owner likes the word medical, give them a better word: named materials, lot trace, oil-contact list.", "Better words still sell."),
    ]),
}


BOOST4 = {
    "n_laser": _notes("Laser last-mile checks", [
        ("Scan the QR", "Scan the first unit QR with a cheap phone before the lot leaves. If it fails, the lot fails.", "Cheap phones are the bar."),
        ("Wipe twice", "One wipe is theatre. Two wipes, two cloths. Oil hides in the etch.", "Theatre ships tickets."),
        ("Count nests", "If nest 4 always drifts, retire nest 4. Do not average it into the lot.", "Nests have names."),
        ("Stop on spelling", "One misspelled unit means check the source file, not the next ten. The file is wrong.", "Ten more will also be wrong."),
    ]),
    "n_30": _notes("3.0 last-mile checks", [
        ("Reset the pen", "Before each 3.0 session, confirm the step is low. Yesterday's demo may have left it high.", "High is a leftover."),
        ("Jar first", "Smell the jar immediately before the cart. A ten-minute gap is a different test.", "Gaps drift."),
        ("Log leftover", "Weigh leftover even on a flavour pass. Flavour can pass while yield fails.", "Yield is oil you bought."),
        ("No booth oil", "If the only 3.0 you tasted was booth oil, you have not sampled. Book the tray.", "Booth oil is advertising."),
    ]),
    "n_uk": _notes("Export last-mile checks", [
        ("Repeat the country", "Last email in the thread should name the destination country. If it does not, ask again.", "Silence is the wrong country."),
        ("Empty means empty", "If a sample was filled then cleaned, say cleaned, not empty. Residue is a fact.", "Facts clear customs better."),
        ("Lithium line", "If batteries are in the carton, the invoice says so. Hidden cells are a seized carton.", "Hidden is seized."),
        ("Counsel ping", "New market, new ping. Do not reuse last year's sentence.", "Years change files."),
    ]),
    "b_terp": _notes("Terpene last-mile checks", [
        ("Low then stop", "If citrus dies, stop. Do not hunt for pepper on a higher step as a consolation.", "Consolation is scorched oil."),
        ("Same battery", "Flavour ladders use one battery SKU. Mixing pens mixes heaters.", "Pens are variables."),
        ("Cap the jar", "Cap the control jar between every tester. Open jars are not controls.", "Open is drift."),
        ("Write burnt", "If it is burnt, write burnt. Do not write complex. Complex is how burnt survives.", "Burnt is a word."),
    ]),
    "b_vs": _notes("Ceramic versus cotton last-mile checks", [
        ("Cut one", "Cut one unit per lot and photograph the heater. If you will not cut, you will not know.", "Knowing is a saw."),
        ("Tote colour", "Ceramic totes one colour, cotton another, forever. When cotton is gone, keep the rule anyway.", "Rules outlive inventory."),
        ("No dual PO", "A PO that lists both heaters as options will be filled with the cheaper one. Separate POs.", "Options become cotton."),
        ("Leftover chart", "Put leftover grams on the quality board this week. Not next quarter.", "Weeks are when habits set."),
    ]),
    "b_leak": _notes("Leak last-mile checks", [
        ("Two photos", "Mouthpiece and pin. Always both. One photo is a guess.", "Guesses scale badly."),
        ("Hold at three", "Three same-path weepers, hold the lot. Do not wait for ten.", "Ten is a shipment."),
        ("Upright pack", "If the insert cannot hold upright, the pack is a leak tool. Change the insert.", "Inserts are geometry."),
        ("Destroy list", "Leakers go on a destroy list the same day. Tomorrow they become testers.", "Testers should not weep."),
    ]),
    "b_tray": _notes("Tray last-mile checks", [
        ("One oil", "Tray one is one oil. A second oil is tray two. Write it on the lid.", "Lids get read."),
        ("Scale morning", "Check the scale the morning you fill the tray. Not last month.", "Mornings are when mass is true."),
        ("24-hour alarm", "Set an alarm for the leak check. Trays without alarms become shelf decoration.", "Decoration is not data."),
        ("No pockets", "Filled tray units do not leave in pockets. Sign that on the door.", "Doors get ignored. Still put it there."),
    ]),
    "b_med": _notes("Materials last-mile checks", [
        ("Name the polymer", "If they cannot name it, you cannot file it. Stop at the name.", "Names are the start of grade."),
        ("Match the lot", "Certificate lot and carton lot must match. Near enough is a souvenir.", "Souvenirs are not files."),
        ("No inhaler on packs", "Unless the licence says so, the pack does not say inhaler. Check the proof.", "Proofs ship."),
        ("Lock the wiki", "If the materials page is editable by sales, it is not a record. Move it.", "Records do not have edit by anyone."),
    ]),
}


BOOST5 = {
    "n_30": _notes("3.0 packing notes", [
        ("Insert line", "The insert should say start low. If there is no insert, the carton panel should say it.", "Silent cartons get maxed."),
        ("Control stay", "Keep the hot-core control until the PO is placed. Throwing it away early restarts the argument.", "Arguments need the control."),
        ("No turbo stickers", "Stickers that imply more vapour will be used on 3.0. Do not print them for live SKUs.", "Stickers are settings."),
        ("Ship cool", "Do not leave 3.0 filled samples in a van to save a trip. Heat is a test you did not schedule.", "Schedule or do not ship."),
    ]),
    "n_uk": _notes("Export packing notes", [
        ("Invoice verbs", "Use empty hardware for licensed fillers on the invoice. Do not use vape juice or consumer kit if that is not true.", "Verbs get read."),
        ("Battery count", "Count cells on the invoice. A wrong count is a lithium paperwork fail.", "Counts are safety."),
        ("Market list", "If the buyer names three countries, the quote names three. Do not write EU as a shortcut.", "EU is not a country."),
        ("No worldwide", "Delete worldwide from templates. Replace with ask us.", "Templates leak."),
    ]),
    "b_terp": _notes("Terpene packing notes", [
        ("Card sync", "Jar card and cart card must not fight about heat. Read them side by side before print.", "Side by side catches fights."),
        ("No rainbow chart", "Do not print a terpene rainbow with Celsius on a 510 insert. Print start low.", "Rainbows become setpoints."),
        ("Rest time", "Let a cart rest after fill before flavour score. Warm fill aroma is not session aroma.", "Rest is a step."),
        ("Two noses", "Live SKUs get two noses or they get a delay. One nose is a maybe.", "Maybe is not a pass."),
    ]),
    "b_vs": _notes("Cotton retirement packing notes", [
        ("PO roots", "Ceramic SKU roots should not resemble cotton roots. Lookalike codes get mis-picked.", "Mis-picks restart cotton."),
        ("Wall photo", "The cutaway photo stays on the wall after cotton is gone so new hires know why.", "Hires did not live the tickets."),
        ("Vendor emails", "Cotton vendor emails go to a dead folder. Do not just see the price.", "Prices restart lines."),
        ("Board packet", "Leftover oil stays in the packet every time ceramic is questioned. Every time.", "Every time is the rule."),
    ]),
    "b_leak": _notes("Leak packing notes", [
        ("Insert photo", "Photograph the insert with a cart in it before the first ship. That photo is the upright claim.", "Claims need photos."),
        ("Retail script", "Script: photograph mouthpiece and pin, then call. Not: it leaked, send a new box.", "New boxes hide rates."),
        ("Hold note", "A hold note on the carton beats a verbal hold. Verbal holds ship.", "Ink holds."),
        ("No tester weeps", "If a tester weeps, it is not a tester. Pull it in front of staff so the rule is seen.", "Seen rules stick."),
    ]),
    "b_tray": _notes("Tray packing notes", [
        ("Lid text", "Write the oil lot on the lid in marker. Cards hide under units.", "Lids are visible."),
        ("Alarm owner", "Name the person who owns the 24-hour alarm. Unowned alarms snooze forever.", "Names get called."),
        ("Cage key", "Two keys. One person on holiday should not freeze the tray in a locked box nobody can open.", "Two keys, one tray."),
        ("Pass stamp", "A red pass stamp on the card is allowed to be ugly. Ugly means it happened.", "Pretty cards lie."),
    ]),
    "b_med": _notes("Materials packing notes", [
        ("Proof pass", "Pack proofs get a materials pass, not only a design pass. Design will put inhaler back.", "Design likes halo words."),
        ("PDF folder", "Certificates live in a dated folder named by lot. Desktop files are lost files.", "Desktops are graves."),
        ("Sales list", "Give sales the banned word list on one page. If it is not one page, they will not use it.", "One page is a tool."),
        ("Owner word", "If the owner wants medical, swap in named materials on the same slide so they still have a strong phrase.", "Strong can be precise."),
    ]),
}


def extra(key: str) -> str:
    parts = [LONG[key], BOOST[key]]
    for blob in (BOOST2.get(key), BOOST3.get(key), BOOST4.get(key), BOOST5.get(key)):
        if blob:
            parts.append(blob)
    return "\n".join(parts)
