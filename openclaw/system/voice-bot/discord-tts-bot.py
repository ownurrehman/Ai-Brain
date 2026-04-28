import discord
from discord.ext import commands
import pyttsx3
import os
import asyncio

# Discord bot setup
intents = discord.Intents.default()
intents.message_content = True
intents.voice_states = True
bot = commands.Bot(command_prefix='!', intents=intents)

# TTS engine (offline, free)
engine = pyttsx3.init()
engine.setProperty('rate', 150)  # Speed
engine.setProperty('volume', 0.9)  # Volume

# Voice channel to join
VOICE_CHANNEL_ID = 973109476724981774  # meeting-room
AUDIO_QUEUE = asyncio.Queue()

@bot.event
async def on_ready():
    print(f'🦞 Voice bot logged in as {bot.user}')
    channel = bot.get_channel(VOICE_CHANNEL_ID)
    if channel:
        await channel.connect()
        print('✅ Joined meeting-room voice channel')

@bot.event
async def on_message(message):
    if message.author == bot.user:
        return
    
    # Listen in claw-chat channel for messages to read aloud
    if message.channel.id == 1476025453599789191:  # claw-chat
        await speak_text(message.content)
        await message.add_reaction('🔊')

async def speak_text(text):
    """Convert text to speech and play in voice channel"""
    try:
        # Generate speech
        audio_file = '/tmp/tts_output.wav'
        engine.save_to_file(text, audio_file)
        engine.runAndWait()
        
        # Play audio in voice channel
        voice = discord.utils.get(bot.voice_clients, guild__id=973109476129402900)
        if voice and voice.is_connected():
            source = discord.FFmpegPCMAudio(audio_file)
            voice.play(source)
            while voice.is_playing():
                await asyncio.sleep(0.1)
        
        # Cleanup
        os.remove(audio_file)
    except Exception as e:
        print(f'TTS Error: {e}')

@bot.command()
async def speak(ctx, *, text: str):
    """Manual TTS command: !speak hello world"""
    await ctx.send(f'Talking: "{text}"')
    await speak_text(text)

@bot.command()
async def join(ctx):
    """Join voice channel"""
    channel = ctx.author.voice.channel
    await channel.connect()
    await ctx.send('✅ Joined voice channel')

@bot.command()
async def leave(ctx):
    """Leave voice channel"""
    voice = discord.utils.get(bot.voice_clients, guild__id=973109476129402900)
    if voice:
        await voice.disconnect()
        await ctx.send('👋 Left voice channel')

# Run bot
bot.run(os.getenv('DISCORD_BOT_TOKEN'))
