"""News posts: laser, 3.0 heating, UK/EU compliance."""

from lib import U, a, bq, h2, h3, hr, ol, p, table, ul


def news_laser() -> str:
    return "\n".join([
        p(
            "Private-label hardware is a freeze of the core, then a mark on the body. "
            f"Laser engraving, colour, and cartons are how brands look unique after the heater already passed leak. {a(U['laser'], 'Justccell laser engraving')} is that second conversation."
        ),
        p(
            "Teams that laser a body before the core passes will laser it twice. "
            "Ink and foil on a mouthpiece that you then change for CR is how artwork dates slip."
        ),
        bq(
            "The Bottom Line",
            "Lock the ceramic and the fill SOP first. Then engrave. "
            "Laser marks must survive oil, alcohol wipes, and a drop. "
            "Packaging dielines wait until the body is frozen.",
        ),
        h2("What private-label hardware actually includes"),
        p(
            "Private label here means your mark on a production device family, not a unique heater invented for one PO. "
            "You pick a passed Justccell platform, then you add colour, laser, and pack."
        ),
        p(
            "That is faster than tooling a new atomizer. It is also how you stay on a core that already has a leak history."
        ),
        h3("Marks that survive oil"),
        p(
            "Laser on metal or certain coatings beats cheap pad print that dissolves in terpene-rich oil. "
            "If the mark sits on a mouthpiece that sees lips and wipes, test alcohol and oil rubs."
        ),
        p(
            "A logo that disappears in week two is a support ticket, not a brand moment."
        ),
        h3("Colourways"),
        p(
            "Colour is coating and plastic resin. It does not change pore size. "
            "Approve colour on the same body SKU you will fill, including the mouthpiece polymer."
        ),
        p(
            "Translucent tanks that show oil level fight some brand colours. "
            "Decide whether the consumer should see the oil. That is a UX choice with a leak-inspection benefit."
        ),
        hr(),
        h2("How laser fits a filling line"),
        p(
            "Engrave before fill when the mark needs orientation and a clean surface. "
            "Engrave after fill only if the process cannot contaminate the airway and you accept the handling risk."
        ),
        h3("Before fill"),
        p(
            "Clean bodies laser well. Fixtures can hold a mouthpiece at a known angle. "
            "This is the default we prefer on quotes."
        ),
        p(
            "If the mouthpiece is CR and two-motion, fixture it in the locked state so the artwork is readable when the consumer first sees it."
        ),
        h3("After fill"),
        p(
            "Filled units are heavier, leakier under clamp, and unhappy about heat near the tank. "
            "Only do this if the laser is on a metal battery sleeve far from the oil."
        ),
        p(
            "Do not laser a glass tank that is already filled. That is how you buy cracks and a terpene smell in the workshop."
        ),
        hr(),
        h2("Artwork rules that prevent a reprint"),
        p(
            "Vector marks, minimum line weight, and a keep-out around buttons and windows. "
            "Tiny type that looks fine on a 4K mock will vanish on a 10 mm band."
        ),
        h3("What to send"),
        ol([
            "Vector logo (not a screenshot).",
            "Clear zone around the fire button and LED.",
            "Which SKU body, including colour.",
            "Whether the mark is on battery, tank sleeve, or mouthpiece.",
        ]),
        p(
            "If WP staff paste a PNG into a slide, rebuild it. "
            "Lasers trace edges. JPEGs make muddy traces."
        ),
        h3("Regulatory marks"),
        p(
            "Batch codes and market marks may be legally required on the pack, the device, or both. "
            "A pretty logo that leaves no room for a lot code is not production-ready."
        ),
        p(
            f"UK finished medicines and CBPMs have labelling lists that are not optional. See the high-level {a(U['n_uk'], 'compliance note')} and your QP."
        ),
        hr(),
        h2("Packaging is part of the same freeze"),
        p(
            "A sleeve that was drawn for Mini Tank will not fit Tank. "
            f"Freeze the body, then {a(U['pack'], 'design the box')}. The other order produces dumpster dielines."
        ),
        table(
            ["Asset", "Freeze after", "Common miss"],
            [
                ["Laser mark", "Passed core + body", "Mark on a mouthpiece you later change"],
                ["Carton", "Final dimensions", "Insert that lets carts lie on their side"],
                ["CR pouch", "Certified pack", "Foil that fights the CR tab"],
            ],
        ),
        h3("Inserts that hold tanks upright"),
        p(
            "Upright inserts cost more than a bag. They also cut leak tickets. "
            f"If you already fight {a(U['b_leak'], 'cheap-cart leaks')}, do not cheap out on the tray."
        ),
        p(
            "Show the insert in the pack test, not only the outer print."
        ),
        h3("Retail display versus shipper"),
        p(
            "A display tray can lie devices on their side for eight hours. "
            "If that is the plan, test it. Otherwise design the display to keep tanks vertical."
        ),
        hr(),
        h2("Common private-label mistakes"),
        p(
            "These are calendar killers."
        ),
        h3("Laser as a substitute for a unique core"),
        p(
            "A logo does not make a cotton core into a rosin device. "
            f"Pick hardware with {a(U['g_oil'], 'the oil map')} first."
        ),
        p(
            "If two brands share a platform, they compete on oil, mark, and pack. "
            "That is normal. Pretending the laser created a new atomizer is not."
        ),
        h3("Changing polymer after the mark is approved"),
        p(
            "A new mouthpiece resin can change laser contrast and CR feel. "
            "Re-approve. Do not assume the old settings burn the same grey."
        ),
        ul([
            "Keep a marked golden sample in QC.",
            "Record laser power and fixture ID.",
            "Reject lots that look 'close enough' in indoor light only.",
        ]),
        hr(),
        h2("MOQ and sample path"),
        p(
            "Colour and laser have minimums because fixtures and inks are real. "
            "Empty hardware samples can be unmarked. Marked samples should use the production fixture."
        ),
        h3("What to approve on a marked sample"),
        p(
            "Contrast, position, spelling, and whether oil wipe removes the mark. "
            "Do not approve only a PDF render."
        ),
        p(
            "If the sample is unfilled, still wipe it with a terpene-rich solvent you actually use, on a scrap unit. "
            "Water is not a terpene."
        ),
        h3("When we will tell you to wait"),
        p(
            "If leak tests on the unmarked body are still open, we will push laser to the next step. "
            "That is not slow. That is cheaper than double laser."
        ),
        hr(),
        h2("Send artwork after the body SKU is named"),
        p(
            f"Name the platform, then attach vectors, then {a(U['contact'], 'request a Justccell quote')} with monthly volume. "
            "Put CR and pack in the same brief if they are in scope."
        ),
        h3("Brief checklist"),
        ul([
            "Body SKU and colour.",
            "Laser location.",
            "Pack: yes/no, CR: yes/no.",
            "Oil family so we do not decorate a core you will abandon.",
        ]),
        h3("Related hub posts"),
        p(
            f"{a(U['n_cr'], 'Child-resistant hardware')} and packaging share the same freeze. "
            "Do not foil over a CR tab."
        ),
        h2("Fixture discipline on the laser"),
        p(
            "A fixture that allows 2 mm of rotation will put your wordmark into the button. "
            "That is not an artist problem. That is a pin or nest problem."
        ),
        p(
            "Number the nests. When a nest wears, only that nest's units drift. "
            "Unnumbered fixtures make every unit a mystery."
        ),
        h3("Contrast on dark versus light bodies"),
        p(
            "Laser on dark coatings often reads as a light etch. Laser on pale polymers can look like a bruise. "
            "Approve both if you sell two colours. One approval does not cover the pair."
        ),
        p(
            "Outdoor sunlight shows contrast the warehouse LED will flatter. "
            "Step outside with the golden sample. It feels silly. It catches ghosts."
        ),
        h3("Spelling and market language"),
        p(
            "If WPML or a translator later changes pack language, the laser on the device may still be English. "
            "That can be fine. It can also be a problem if the laser includes a claim. Keep claims on the pack when you can."
        ),
    ])


def news_30() -> str:
    return "\n".join([
        p(
            "Justccell 3.0 is the ultra-low-temperature ceramic heating generation for oils that lose character on hotter cores. "
            f"The public page lists cottonless ceramic, a heating film meant to cut hot spots, and an airway specified for leak and clog resistance. {a(U['three'], 'Read the 3.0 product story')} before you treat it as a sticker."
        ),
        p(
            "Live resin and live rosin are the oils that make this generation worth a sample tray. "
            "Distillate can still run on 3.0. The point of the generation is the oils that burnt on older heaters."
        ),
        bq(
            "Key Findings",
            "3.0 is a heater specification: lower peak temperature, ceramic pores, isolated airway. "
            "It is aimed at terpene-forward extracts. "
            "Voltage abuse on a 510 battery can still scorch a 3.0 cart.",
        ),
        h2("What Justccell 3.0 changes in the heater"),
        p(
            "Older cores fail live oils in two ways: a hot spot that scorches, and a pore or wick that clogs. "
            "3.0 is specified to even the film temperature and keep oil moving in one direction through ceramic."
        ),
        p(
            "Cottonless is not a slogan here. It means there is no fibre to char when the oil is thick and the session is long."
        ),
        h3("True-to-source flavour"),
        p(
            "The 3.0 copy talks about reducing reheated oil sitting in the core. "
            "Reheated oil is how a cart tastes like last night's session by Friday."
        ),
        p(
            "If your oil is a botanical-terpene distillate, you may notice a smaller gap versus a hot core. "
            "If your oil is live rosin, the gap is the product."
        ),
        h3("Leak and clog path"),
        p(
            "Interconnected heating channels are meant to give oil a predictable path so it is less likely to flood the mouthpiece or starve the core. "
            "That still needs a fill SOP. 3.0 is not a licence to brim-fill."
        ),
        p(
            f"Keep {a(U['g_fill'], 'cap timing and headspace')} even on a 3.0 tray."
        ),
        hr(),
        h2("Which oils should see 3.0 first"),
        p(
            "Put live rosin and high-terpene live resin at the front of the queue. "
            "Put thick distillate second. Put mystery blends last until you name the dominant fraction."
        ),
        h3("Rosin"),
        p(
            f"{a(U['rosin_bar'], 'Rosin Bar')} sits in the all-in-one catalog as rosin-ready with partitioned atomization on the card. "
            "3.0 heating is the broader generation story that such SKUs sit inside."
        ),
        p(
            "Solventless oil plus a cotton wick is a support queue. "
            "Solventless oil plus a low-temp ceramic is a sample plan."
        ),
        h3("Live resin"),
        p(
            "Hydrocarbon live resin still fights generic carts on leak and flavour. "
            "Flexcell Pro, Voca Pro, Blanc, and Slym are the usual first live-resin tray on the Justccell oil map."
        ),
        p(
            "3.0 is the reason those trays exist as more than colours. "
            "Ask for 3.0-capable SKUs by name on the quote, not 'whatever is newest'."
        ),
        hr(),
        h2("How voltage can undo a low-temp core"),
        p(
            "A heater designed for a lower peak still sees the power you send. "
            f"A 3.6 V step on a 510 pen can push a 3.0 cart into the same scorched band you left. {a(U['g_volt'], 'Voltage by oil type')} stays required."
        ),
        table(
            ["Oil", "Battery habit", "3.0 role"],
            [
                ["Live rosin", "Lowest step", "Protects the fraction you paid for"],
                ["Live resin", "Low band", "Keeps citrus and pine from flattening"],
                ["Distillate", "Low then mid", "Still even heat, less drama"],
            ],
        ),
        h3("Closed devices hide the number"),
        p(
            "All-in-ones fire at a designed band. You choose the device rather than a volt number. "
            "If an all-in-one is harsh on your rosin, change SKU. Do not invent a secret button combo as policy."
        ),
        p(
            "Pods with named modes need their own ladder. Do not copy 510 voltages onto Dart modes."
        ),
        h3("What we measure versus what a blog claims"),
        p(
            "Public terpene boiling points (pinene near 156 C, myrcene near 167 C, limonene near 176 C at atmospheric pressure) explain why hot cores flatten top notes. "
            "They are not a thermostat inside your pen. Use them as a why, not as a setpoint."
        ),
        hr(),
        h2("How to sample 3.0 without fooling yourself"),
        p(
            "Fill 3.0 hardware with the production oil, not a booth distillate. "
            "Run the same cap clock you will use in production."
        ),
        h3("Blind a hot-core control"),
        p(
            "Keep one older core SKU on the same oil as a control. "
            "If staff cannot tell them apart, maybe your oil is a simple distillate and 3.0 is optional, not mandatory."
        ),
        p(
            "If they can tell them apart in one session, freeze 3.0 for that SKU. "
            "Do not 'cost engineer' back to cotton after the brand story is shot."
        ),
        h3("Log leftover oil"),
        p(
            "Weigh leftover oil when flavour dies. "
            "A core that tastes fine but strands a third of the tank is still a bad buy."
        ),
        ol([
            "Same fill mass.",
            "Same battery and starting step.",
            "Notes at 25 percent, 50 percent, and end of tank.",
            "Leftover mass at end.",
        ]),
        hr(),
        h2("3.0 versus 'ceramic' as a word on a box"),
        p(
            "Every supplier now says ceramic. 3.0 is a generation with a film, pore, and airway story. "
            "Ask for the drawing. If the drawing is a lifestyle photo, you are buying a word."
        ),
        h3("Questions that belong on the quote"),
        ul([
            "Is this SKU on the 3.0 heater generation?",
            "What oil family was it sampled on?",
            "What battery and voltage were used in that sample?",
        ]),
        p(
            f"{a(U['n_cer'], 'Wholesale ceramic RFQs')} should include those three lines in 2026."
        ),
        h3("Manufacture and QC"),
        p(
            f"{a(U['manufacture'], 'Manufacturing notes')} exist because production-ready means repeatable pores, not a hero unit. "
            "Ask how lots are traced if you will scale."
        ),
        hr(),
        h2("Ask for a 3.0 tray against the oil you fill"),
        p(
            f"Send oil type and fill volume on the {a(U['contact'], 'contact form')}. "
            "If terpene retention is the brief, say so. We will not assume a distillate cart for a rosin brand."
        ),
        h3("What to attach"),
        p(
            "A viscosity note and whether you need laser-ready parts. "
            "Vision Box trays exist for that kind of evaluation. Ask if they fit your stage."
        ),
        p(
            f"Hardware-by-oil grouping is also on {a(U['choose'], 'choose hardware')}. Use it."
        ),
        h3("Limitation"),
        p(
            "3.0 will not legalise an oil. It will not fix a brim fill. "
            "It will not survive a 4 V box mod used as a 'clog clearer'."
        ),
        h2("Why fillers moved off hot metal coils"),
        p(
            "Hot metal plus cannabis extract was a flavour problem and a metals-testing problem. "
            "The industry moved toward ceramic because the heater is the part that touches the oil."
        ),
        p(
            "3.0 is a continuation of that move with a lower peak as the selling point. "
            "If your RFQ still says 'coil' meaning a wire wrap, you are writing 2016."
        ),
        h3("Isolated airways"),
        p(
            "An isolated airway keeps vapour from dragging across leftover oil films in the wrong chamber. "
            "That is a drawing detail. Ask to see it."
        ),
        p(
            "If oil and vapour share a dirty path, you taste the path. "
            "Consumers call that 'hardware taste'. They are not wrong."
        ),
        h3("Do not skip the control cart"),
        p(
            "Without a control, every new generation tastes 'better' because the room wants it to. "
            "Blind the jars. It keeps you honest when the invoice is higher."
        ),
    ])


def extra_news_30() -> str:
    return ""


def news_uk() -> str:
    return "\n".join([
        p(
            "Justccell sells empty hardware from a UK site to licensed filling partners. "
            "We do not turn an oil legal by shipping a cart. Geography, licence, and finished-product rules sit on your side of the quote."
        ),
        p(
            "UK consumer nicotine vapes sit under TRPR and MHRA notification. Licensed cannabis medicines and CBPMs sit under medicines and mis-use-of-drugs rules. "
            f"Those posters are not interchangeable. {a(U['about'], 'Justccell')} will not collapse them into one 'vape law' graphic."
        ),
        bq(
            "Key Findings",
            "Empty hardware is not a finished consumer product. "
            "UK nicotine TRPR limits do not automatically apply to a cannabis all-in-one you fill abroad. "
            "Name the market on the RFQ so CR, labels, and tank volume are not copied from the wrong poster.",
        ),
        h2("What this site can and cannot claim"),
        p(
            "The storefront is inquiry-first wholesale hardware. "
            "Pages describe devices, fill notes, and process. They are not a licence, a MHRA notification, or a legal opinion."
        ),
        p(
            "If your counsel needs a letter, they should write from your files, not from a blog paragraph."
        ),
        h3("Empty versus filled"),
        p(
            "Empty carts crossing a border are a different customs and product-safety story from filled consumer goods. "
            "Do not assume a hardware invoice covers a filled SKU you assemble later."
        ),
        p(
            "If you fill in the UK, you own the finished product rules. "
            "If you fill in another country and import, you own that path too."
        ),
        h3("3Devices and Justccell"),
        p(
            "The commercial site is justccell.com. The client entity is 3Devices. "
            "Your contracts and invoices should match the entity you actually trade with. Blogs do not change that."
        ),
        hr(),
        h2("UK nicotine rules people paste by accident"),
        p(
            "TRPR 2016 sets child-resistant and tamper-evident rules for nicotine e-cigarettes and refill containers, plus tank and bottle size limits for nicotine liquids. "
            "GOV.UK's e-cigarette guidance is written for that consumer nicotine category."
        ),
        p(
            "A 2 ml nicotine tank cap is not a secret international law for every oil device. "
            "If you make a UK consumer nicotine product, follow TRPR and notify. If you do not, stop putting 2 ml on the drawing because a blog scared you."
        ),
        h3("When TRPR is relevant to you"),
        p(
            "It is relevant if you actually place nicotine-containing consumer vapes on the GB or NI market. "
            "Then MHRA notification, ingredients bans, and labelling are your operations problem."
        ),
        p(
            "Justccell catalog photos of 2 ml and 3 ml cannabis-oriented tanks are hardware options. "
            "They are not a TRPR compliance kit."
        ),
        h3("When TRPR is a distraction"),
        p(
            "It is a distraction if your SKU is a licensed cannabis extract device for a market that is not UK nicotine retail. "
            "Use that market's rules. Do not inherit 20 mg/ml nicotine caps as folklore."
        ),
        hr(),
        h2("UK cannabis medicines are a different stack"),
        p(
            "Unlicensed CBPMs have MHRA guidance on manufacture, import, labelling, and 'keep out of reach of children'. "
            "That stack is medicines plus controlled-drug marking. It is not a Discover-page DIY."
        ),
        p(
            "If you are not on that path, do not copy CBPM label lists onto a lifestyle cart. "
            "If you are on that path, your QP already knows the list."
        ),
        h3("Hardware still matters on a medicine path"),
        p(
            "Extractables, leak, and dose delivery still sit in quality files. "
            "A leaking cart is a quality event even when the oil is lawful."
        ),
        p(
            f"{a(U['n_cr'], 'Child-resistant layers')} may be pack-led on medicines. Follow the file, not a US cannabis Instagram pack."
        ),
        h3("Spain and Switzerland site prefixes"),
        p(
            "justccell.com is the UK order site. /es/ and /ch/ are landings, not separate legal entities by themselves. "
            "VAT, licence, and who may buy still follow the account and the destination. Ask on the quote."
        ),
        hr(),
        h2("EU-facing hardware programmes"),
        p(
            "EU nicotine vapes have TPD-style rules that look cousin to TRPR. "
            "EU cannabis rules are national and messy. Do not use Germany as a stand-in for Poland."
        ),
        p(
            "If you ship empty hardware into the EU, you still need a product-safety and customs story. "
            "If you ship filled goods, you need more than a story."
        ),
        h3("What to put on an RFQ"),
        ul([
            "Destination countries, not 'Europe'.",
            "Filled or empty at the border.",
            "Nicotine consumer vs cannabis licensed vs other.",
            "Whether you need CR pack tests named by protocol.",
        ]),
        h3("What we will not do"),
        p(
            "We will not file your MHRA notification. We will not be your importer of record unless a contract says so. "
            "We will not hide a licence problem behind a ceramic core."
        ),
        p(
            f"{a(U['contact'], 'Contact Justccell')} with the market list. The sample tray comes after the list is honest."
        ),
        hr(),
        h2("Labelling and claims on hardware blogs"),
        p(
            "This article will not tell you a health claim to print on a cart. "
            "Medical claims turn hardware copy into a regulated communication. Leave them to the people who own the licence."
        ),
        table(
            ["Document type", "Owner", "Hardware blog role"],
            [
                ["Device sell sheet", "Brand + supplier", "Materials, volumes, voltage"],
                ["Finished pack label", "Licence holder", "Legal marks, not lifestyle"],
                ["This Discover post", "Justccell", "Process, not permission"],
            ],
        ),
        h3("Language on justccell.com"),
        p(
            "WPML may show more than English. Translations do not create new licences. "
            "If a German string appears, it is a language, not a DE market authorisation."
        ),
        p(
            "Keep claims conservative on translated packs. Translators like adjectives. Regulators do not."
        ),
        h3("Records"),
        p(
            "Keep lot codes, incoming inspection, and leak data. "
            "When a market asks what you shipped, blogs will not save you. Files will."
        ),
        hr(),
        h2("Ask with a market list, not a vibe"),
        p(
            "Name countries and whether units ship empty. "
            f"Then request hardware against the {a(U['g_oil'], 'oil you fill')}."
        ),
        h3("Useful attachments"),
        ol([
            "Licence category in one sentence.",
            "Destination countries.",
            "Need for CR pack protocol, if any.",
            "Oil family.",
        ]),
        h3("Related process posts"),
        p(
            f"{a(U['n_cer'], 'Ceramic RFQs')} and {a(U['n_laser'], 'private-label marks')} still apply after the legal path is named. "
            "Law first, logo second."
        ),
        h2("Customs and the word 'samples'"),
        p(
            "Sample trays are still goods. They have values, materials, and sometimes residual oil if you mishandle returns. "
            "Do not write 'no commercial value' as a personality. Write the true value and the true contents."
        ),
        p(
            "If a sample is filled, say it is filled. "
            "A customs officer who finds oil in an 'empty cart' will not care that it was a flavour test."
        ),
        h3("Batteries in the same box"),
        p(
            "Lithium cells have transport rules. A mixed carton of carts and batteries is not 'just accessories'. "
            "Your forwarder already knows this. Your marketing intern might not. Put the forwarder in the thread."
        ),
        p(
            f"Charge and storage notes sit in {a(U['g_charge'], 'the 510 charging guide')}. Transport sits with the forwarder."
        ),
        h3("Limitation, again"),
        p(
            "This is a hardware supplier blog on a site that is still in coming-soon for the public. "
            "It is not updated case law. Recheck with counsel before you print a pack."
        ),
    ])


def extra_news_uk() -> str:
    return ""


def extra_news_laser() -> str:
    return ""
