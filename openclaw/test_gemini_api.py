import google.generativeai as genai
import os
import sys

# Try to use the API key from environment or get from user
api_key = os.environ.get("GOOGLE_API_KEY")
if not api_key:
    print("No GOOGLE_API_KEY found in environment")
    sys.exit(1)

genai.configure(api_key=api_key)

# Try to generate an image
model = genai.GenerativeModel('gemini-2.0-flash-exp-image-generation')

response = model.generate_content("Generate an image of a futuristic lobster wearing a VR headset")

print(f"Response: {response}")
for part in response.parts:
    if hasattr(part, 'inline_data') and part.inline_data:
        print(f"Got image data! Mime type: {part.inline_data.mime_type}")
        # Save the image
        import base64
        data = part.inline_data.data
        with open('/tmp/gemini_test_image.png', 'wb') as f:
            f.write(data)
        print("Image saved to /tmp/gemini_test_image.png")
    else:
        print(f"Text: {part.text if hasattr(part, 'text') else part}")
