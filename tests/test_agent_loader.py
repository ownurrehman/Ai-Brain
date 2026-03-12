from pathlib import Path
import unittest

from ai_brain.agent_loader import compose_agent_payload, list_agents, load_agent


class AgentLoaderTest(unittest.TestCase):
    def test_lists_example_agents(self) -> None:
        agents = list_agents()
        self.assertEqual(agents, ["coder", "researcher", "strategist"])

    def test_loads_researcher_in_expected_order(self) -> None:
        definition = load_agent(agent="researcher")
        self.assertEqual(
            [document.name for document in definition.documents],
            ["identity.md", "instructions.md", "tasks.md", "tools.md", "memory.md"],
        )

    def test_loads_coder_in_expected_order(self) -> None:
        definition = load_agent(agent="coder")
        self.assertEqual(
            [document.name for document in definition.documents],
            [
                "identity.md",
                "instructions.md",
                "coding_standards.md",
                "rankray-hq.md",
                "seo-engineering.md",
                "wordpress-plugin-development.md",
                "tools.md",
                "verification.md",
                "memory.md",
            ],
        )

    def test_supports_loading_multiple_agents(self) -> None:
        researcher = load_agent(agent="researcher")
        coder = load_agent(agent_path=Path("ai_brain/agents/coder"))
        researcher_payload = compose_agent_payload(researcher)
        coder_payload = compose_agent_payload(coder)

        self.assertIn("You are a researcher.", researcher_payload)
        self.assertIn("RankRay HQ application development", coder_payload)
        self.assertNotEqual(researcher_payload, coder_payload)


if __name__ == "__main__":
    unittest.main()
