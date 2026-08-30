> **Parent Hub:** [[memory/entities/INDEX|🌐 Entity Knowledge Graph Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🏥 Industry Ontology: Healthcare & Physiotherapy

> **Semantic schema and entity ontology mapping for orthopedic rehabilitation, pain management, and Canadian health insurance.**

---

## 🗺️ Core Entity Hierarchy

```mermaid
graph TD
    Physio["Physiotherapy & Rehabilitation"] --> Modalities["Clinical Modalities"]
    Physio --> Conditions["Musculoskeletal Conditions"]
    Physio --> Insurance["Canadian Insurance & Claims"]

    Modalities --> Manual["Manual Therapy & Joint Mobilization"]
    Modalities --> Tech["Shockwave & Spinal Decompression"]
    Modalities --> Needling["Dry Needling & Medical Acupuncture"]

    Conditions --> Spine["Herniated Disc, Sciatica, Lower Back Pain"]
    Conditions --> Joint["Knee Osteoarthritis, Rotator Cuff Tear"]
    Conditions --> Trauma["Whiplash, Concussion, Ankle Sprain"]

    Insurance --> DirectBill["Telus Health & Private Extended Health"]
    Insurance --> WSIB["Workplace Safety & Insurance Board (Ontario)"]
    Insurance --> MVA["Motor Vehicle Accident (Auto Insurance HCAI)"]
```

---

## 📌 Standard Entity Triples for Content & Schema
- `Physiotherapist` $\rightarrow$ `registered with` $\rightarrow$ `College of Physiotherapists of Ontario`
- `Extracorporeal Shockwave Therapy` $\rightarrow$ `stimulates` $\rightarrow$ `Tissue Neovascularization & Collagen Synthesis`
- `WSIB Claims` $\rightarrow$ `cover` $\rightarrow$ `Workplace Injury Rehabilitation with Zero Out-Of-Pocket Fees`
