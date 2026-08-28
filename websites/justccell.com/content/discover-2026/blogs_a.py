"""Five Blogs posts for justccell.com Discover."""

from lib import U, a, bq, h2, h3, hr, ol, p, table, ul


def blog_terpenes() -> str:
    return "\n".join([
        p(
            "Terpenes are the volatile aromatic compounds that make an extract smell like citrus, pine, pepper, or florist shop. "
            f"They are also the first things a hot core destroys. {a(U['tech'], 'Hardware temperature')} is not a side quest if you sell flavour."
        ),
        p(
            "Pinene boils near 156 C, myrcene near 167 C, limonene near 176 C, and linalool near 199 C at atmospheric pressure (NIST-aligned figures commonly cited in terpene references). "
            "Your pen is not a boiling-point bath. Those numbers still explain why a 3.6 V session tastes flatter than the jar."
        ),
        bq(
            "The Bottom Line",
            "Terpenes leave first when the heater runs hot. "
            "Ceramic low-temp cores and low 510 voltage steps protect the fraction you paid for. "
            "Boiling-point charts are a why, not a thermostat.",
        ),
        h2("What terpenes are doing in a cartridge"),
        p(
            "In the plant they are oils in trichomes. In a cart they are dissolved or blended in the extract. "
            "In the heater they are the lightest useful molecules you are trying to vaporise without cooking."
        ),
        p(
            "Monoterpenes (pinene, myrcene, limonene) are smaller and more volatile than many sesquiterpenes. "
            "That is why a harsh, hot cart can still smell woody while the citrus is gone."
        ),
        h3("Native versus added terpenes"),
        p(
            "Live resin and live rosin carry native terpene fractions from the plant process. "
            "Distillate often gets botanical or cannabis-derived terpenes added back. Both can be ruined by the same hot spot."
        ),
        p(
            "Added botanicals can be even more obvious when they scorch. "
            "A lemon fraction that goes to furniture polish is a hardware plus voltage problem, not 'the strain'."
        ),
        h3("Oxidation is a second death"),
        p(
            "Terpenes also oxidise in air. Heat speeds that. "
            "A delayed cap on a warm fill is a flavour process, not only a leak process."
        ),
        p(
            f"{a(U['g_fill'], 'Cap clocks')} protect more than carpets. They protect the top notes."
        ),
        hr(),
        h2("Why boiling-point charts mislead operators"),
        p(
            "Atmospheric boiling point is a lab number at 1 atmosphere. "
            "A cart is a tiny, uneven, reduced-pressure-ish, airflow-dependent mess. You will not hit 176 C as a clean plateau."
        ),
        p(
            "Terpenes and Testing Magazine has warned that copied charts mix vacuum boiling points with atmospheric ones, especially for caryophyllene and humulene. "
            "If a chart says humulene boils at 106 C, treat it as a suspect vacuum figure, not a pen setting."
        ),
        h3("Use charts as ranking, not setpoints"),
        p(
            "Ranking is useful: pinene and myrcene are fragile relative to many heavier compounds. "
            "Setpoints are not useful unless you instrument the core."
        ),
        p(
            "We do not print a Celsius target on 510 pens because the board does not offer one. "
            "We print voltage steps and oil families."
        ),
        h3("What actually changes flavour on a line"),
        ul([
            "Peak heater temperature (core + voltage).",
            "Time oil sits hot in the core between draws.",
            "Air and cap delay after a warm fill.",
            "Leftover oil that keeps cycling through the heater.",
        ]),
        hr(),
        h2("How hardware temperature is set in practice"),
        p(
            "On 510 batteries you set volts. On some pods you set named modes. "
            f"On many all-in-ones you set nothing. {a(U['g_volt'], 'Voltage by extract')} is the 510 version of this story."
        ),
        h3("Ceramic versus a wire and wick"),
        p(
            "Ceramic spreads heat and wicks without cotton char. "
            f"A wire on cotton hits faster and then tastes like the wick. {a(U['b_vs'], 'Ceramic versus cotton')} is the full comparison."
        ),
        p(
            "Even ceramic has a peak. Film heaters exist to drop that peak. "
            f"That is the {a(U['n_30'], 'Justccell 3.0')} generation claim."
        ),
        h3("Airflow"),
        p(
            "Tight airflow plus high voltage is how you torch a small core. "
            "If a brand wants huge clouds on live resin, they are asking for a different oil or a different honest claim."
        ),
        p(
            "Do not open airflow and voltage together as a 'fix'. Change one variable per test."
        ),
        hr(),
        h2("Oil families and terpene risk"),
        p(
            "Live rosin is usually the highest risk. Live resin is next. Distillate with a light botanical cut can still scorch but has less native complexity to lose."
        ),
        table(
            ["Oil", "Terpene situation", "Hardware habit"],
            [
                ["Live rosin", "Native, fragile", "Lowest temp ceramic, lowest volts"],
                ["Live resin", "Native, variable viscosity", "Ceramic, low volts, strict cap"],
                ["Distillate + botanicals", "Added fraction", "Even heat, do not max the pen"],
            ],
        ),
        h3("Viscosity versus aroma"),
        p(
            "A thin live resin can leak and still be terpene-rich. "
            "A thick distillate can sit still and still burn if you over-volt. Do not use thickness as a flavour proxy."
        ),
        p(
            f"{a(U['oil'], 'Oil type notes')} keep those families separate for a reason."
        ),
        h3("Storage"),
        p(
            "Heat in a van strips aroma before the consumer. "
            "Filled goods want cool, dark, upright storage. That is operations, not a heater brochure."
        ),
        hr(),
        h2("How to test whether your core is killing terpenes"),
        p(
            "Smell the jar. Hit the cart at the lowest step. Compare. "
            "If the cart smells like hot plastic and the jar smells like fruit, you have a temperature or materials problem."
        ),
        h3("Simple ladder"),
        ol([
            "Same oil lot, two cores or two voltage steps.",
            "Blind the devices for staff.",
            "Score aroma match, harshness, and leftover oil.",
            "Keep the winner's settings on the SKU card.",
        ]),
        h3("Lab tests"),
        p(
            "If you have a lab, terpene panels before and after a standardised session are the adult version. "
            "Most filling partners will not run that on every lot. The sniff-and-ladder still catches disasters."
        ),
        p(
            "Do not trust a single enthusiastic salesperson hit at a booth. Booth HVAC is not your warehouse."
        ),
        hr(),
        h2("Common terpene myths on hardware teams"),
        p(
            "Myths waste trays."
        ),
        h3("'Crank it to taste the strain'"),
        p(
            "Cranking it tastes the heater. "
            "Strain character is a low-temp job."
        ),
        p(
            "If vapour is weak at low volts, check fill, clog, and whether the oil is too thick for that airway. "
            "Then step up once. Not four times."
        ),
        h3("'Ceramic means I can ignore voltage'"),
        p(
            "Ceramic is a material. Voltage is power. "
            "Together they set the peak. Ignore either and you are back to a hot-metal story with nicer marketing."
        ),
        hr(),
        h2("Keep the jar and the cart in the same conversation"),
        p(
            f"When you {a(U['contact'], 'request Justccell samples')}, name the oil and whether flavour retention is the brief. "
            "We will not assume a distillate heater for a rosin SKU."
        ),
        h3("What to write on the brief"),
        ul([
            "Oil family and whether terpenes are native or added.",
            "Battery SKU and starting step you plan to print.",
            "Whether you need 3.0-generation heaters.",
        ]),
        h3("Related guides"),
        p(
            f"{a(U['g_oil'], 'Choose hardware by oil')} and the 3.0 news post are the practical pair to this essay."
        ),
        h2("Caryophyllene charts and why we flinch"),
        p(
            "Peppery caryophyllene is often printed at 119-130 C on lifestyle charts. "
            "Atmospheric boiling point is much higher. Those low figures are typically reduced-pressure measurements copied as if they were pen temperatures."
        ),
        p(
            "If your team is targeting 130 C to 'get the pepper', they may be reading a vacuum number. "
            "Stop using that chart for 510 settings."
        ),
        h3("What to tell retail staff"),
        p(
            "Tell them to start low and stop if the citrus dies. "
            "Do not give them a terpene-by-terpene Celsius treasure map. They will not have a thermocouple."
        ),
        p(
            "One honest range on the insert beats a rainbow infographic."
        ),
        h3("Packaging aroma"),
        p(
            "A carton that smells like the oil can mean a leak. "
            "It can also mean a scented insert. Do not scent inserts if you need leak detection by nose."
        ),
    ])


def extra_blog_terp() -> str:
    return ""


def blog_vs() -> str:
    return "\n".join([
        p(
            "Cotton wicks wrapped on a wire heat fast and then degrade. "
            f"Ceramic cores wick through pores and heat as the ceramic. For thick cannabis oils, that difference is the product. {a(U['cartridge'], 'Justccell cartridges')} are specified on the ceramic side of that split."
        ),
        p(
            "Cotton still appears in cheap RFQs because it is familiar from nicotine liquids. "
            "Live resin and rosin are not those liquids."
        ),
        bq(
            "Quick Comparison",
            "Ceramic pores move viscous oil and avoid fibre char. "
            "Cotton is cheaper and faster to heat, then it burns and gums on thick extracts. "
            "If the oil is live or heavy, specify ceramic and test on your fill SOP.",
        ),
        h2("What each heater is made of"),
        p(
            "A cotton system is a metal coil plus a fibre that holds oil against the coil. "
            "A ceramic system is a sintered porous body that is both sponge and hot plate."
        ),
        p(
            "When oil is thin, cotton can keep up. When oil is thick, it sits on the fibre, the coil flashes, and you get a burnt note plus leftover oil."
        ),
        h3("Metal in the oil path"),
        p(
            "Wires and mystery posts sit in the oil on many cotton carts. "
            "Ceramic designs can still include metal posts. Ask. Full ceramic paths exist because testers got tired of unexplained metals and taste."
        ),
        p(
            f"{a(U['n_cer'], '2026 wholesale RFQs')} should name the post material, not only 'ceramic'."
        ),
        h3("Heat-up time"),
        p(
            "Cotton and quartz can feel snappier on the first millisecond. "
            "Consumers sometimes call ceramic 'slow'. Fillers should call it 'even'. Snap is not a virtue if it scorches myrcene."
        ),
        p(
            "If a brand insists on instant huge vapour on rosin, they are asking for a cloud, not a terpene. "
            "Write that down so the later ticket does not surprise anyone."
        ),
        hr(),
        h2("How each fails on live oils"),
        p(
            "Failure modes are the buying guide."
        ),
        table(
            ["Heater", "Typical live-oil failure", "What you see"],
            [
                ["Cotton + wire", "Char and gum", "Burnt taste, blackened wick"],
                ["Ceramic", "Flooded chimney if filled badly", "Oil in mouthpiece, core still intact"],
                ["Either", "Over-voltage", "Flat aroma, hot body"],
            ],
        ),
        h3("Cotton's leftover oil"),
        p(
            "Fibre that will not wick the last third of a thick tank is a yield problem. "
            "You paid for oil that becomes furniture stain in the cartridge graveyard."
        ),
        p(
            "Weigh leftovers. It is unglamorous and it ends arguments."
        ),
        h3("Ceramic's false blame"),
        p(
            "Ceramic gets blamed for leaks that are cap delays. "
            f"Sort {a(U['b_leak'], 'leak paths')} before you change pore size."
        ),
        p(
            "A perfect ceramic core with a brim fill is still a leak generator. "
            "Materials do not repeal headspace."
        ),
        hr(),
        h2("Cost, honestly"),
        p(
            "Ceramic units cost more on the PO. Cotton units cost more in returns if the oil is hard. "
            "Run the numbers on your ticket rate, not on the unit spreadsheet alone."
        ),
        h3("When cotton can still be rational"),
        p(
            "Thin oils, low terpene load, markets that only allow those oils, and a filling line that already controls voltage tightly. "
            "If that is not you, stop romanticising fibre."
        ),
        p(
            "A nicotine-era SOP is not a cannabis SOP. "
            "Copying it is how you 'save' 8 cents and spend a pound."
        ),
        h3("Pilot math"),
        ol([
            "Fill 200 of each heater with the same oil lot.",
            "Track leak, burnt flavour, leftover mass.",
            "Add support hours.",
            "Then look at unit price.",
        ]),
        hr(),
        h2("How to specify the winner on a PO"),
        p(
            "Write ceramic, tank material, oil family, and voltage band. "
            "Do not write 'like the cotton one but nicer'."
        ),
        h3("Drawings"),
        p(
            "Ask for a cutaway. If cotton is still in the heater, it will show. "
            "If the supplier refuses a cutaway, they are selling a silhouette."
        ),
        p(
            f"{a(U['ceramic'], 'Ceramic-EVOMAX')} and TH2/M6T families are named so you can PO a drawing, not a mood."
        ),
        h3("Closed systems"),
        p(
            "All-in-ones such as Blanc advertise a full ceramic device. "
            "That is a different architecture from a 510 glass cart with a ceramic heater. Use the right name."
        ),
        p(
            "Pods are closed too. Their heater is not a 510 cotton cart with a magnet."
        ),
        hr(),
        h2("Staff training differences"),
        p(
            "Cotton trains people to fear dry hits and to over-wet. "
            "Ceramic trains people to fear flooded chimneys and overfills. Train the actual heater you ship."
        ),
        h3("Retail scripts"),
        ul([
            "Cotton: do not run the tank bone-dry on high heat.",
            "Ceramic: start low voltage, do not brim-fill, keep upright.",
            "Both: stop if it tastes burnt.",
        ]),
        h3("QC scripts"),
        p(
            "Cotton QC looks into the tank for blackened fibre if the design allows. "
            "Ceramic QC looks at mouthpiece oil, pin oil, and first-hit flavour. Different checklists."
        ),
        hr(),
        h2("Pick ceramic for hard oils, then prove it"),
        p(
            f"{a(U['contact'], 'Request a ceramic tray')} against the oil you fill. "
            "Bring a cotton control if you still need to convince finance."
        ),
        h3("What finance needs"),
        p(
            "Leftover oil mass and return rate. "
            "Not a story about innovation."
        ),
        h3("Related"),
        p(
            f"{a(U['g_oil'], 'Oil-first hardware')} and 3.0 heating sit on the same side of this comparison."
        ),
        h2("Quartz footnotes"),
        p(
            "Quartz cores heat faster than ceramic and can overshoot. "
            "They show up in some concentrate tools more than in mass 510 oil carts. If a supplier says quartz, run the same scorched-terpene test you would on cotton."
        ),
        p(
            "Do not treat quartz as automatically 'cleaner'. "
            "Clean is a peak temperature and a materials certificate."
        ),
        h3("Hybrid marketing language"),
        p(
            "Boxes that say ceramic when a wick is still present are a reject. "
            "Cutaway or return to sender."
        ),
        p(
            "If you already printed 10,000 boxes, you still have a hardware problem. "
            "The box will not wick the oil."
        ),
        h3("Limitation"),
        p(
            "This comparison is for cannabis extract hardware on filling lines. "
            "It is not a nicotine e-liquid coil review. Different viscosities, different rules."
        ),
    ])


def extra_blog_vs() -> str:
    return ""
