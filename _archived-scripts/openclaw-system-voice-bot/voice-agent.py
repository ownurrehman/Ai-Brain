import discord
from discord.ext import commands
import subprocess
import os
import asyncio
import aiohttp
import io

intents = discord.Intents.default()
intents.message_content = True
intents.voice_states = True
bot = commands.Bot(command_prefix='!', intents=intents)

VOICE_CHANNEL_ID = 973109476724981774  # meeting-room
GUILD_ID = 973109476129402900

@bot.event
async def on_ready():
    print(f'🦞 Voice bot ready: {bot.user}')
    print(f'Guild ID: {GUILD_ID}')
    print(f'Voice Channel ID: {VOICE_CHANNEL_ID}')
    guild = bot.get_guild(GUILD_ID)
    if guild:
        print(f'✅ Found guild: {guild.name}')
        channel = bot.get_channel(VOICE_CHANNEL_ID)
        if channel:
            print(f'✅ Found voice channel: {channel.name}')
            try:
                voice_client = await channel.connect()
                print(f'✅ Auto-joined #meeting-room')
                print(f'Voice connected: {voice_client.is_connected()}')
            except Exception as e:
                print(f'❌ Voice connect error: {type(e).__name__}: {e}')
                import traceback
                traceback.print_exc()
        else:
            print(f'❌ Channel not found: {VOICE_CHANNEL_ID}')
    else:
        print(f'❌ Guild not found: {GUILD_ID}')

@bot.event
async def on_message(message):
    if message.author == bot.user:
        return
    
    # Listen for !speak command or @mentions
    if message.channel.id == 1476025453599789191:  # claw-chat
        if message.content.startswith('!speak ') or bot.user.mentioned_in(message):
            text = message.content.replace('!speak ', '')
            if text.strip():
                await message.channel.send(f'🔊 Speaking: "{text}"')
                await speak_in_voice(text)

async def speak_in_voice(text):
    """Generate speech with macOS say command and play in voice channel"""
    try:
        voice = discord.utils.get(bot.voice_clients, guild__id=GUILD_ID)
        if not voice or not voice.is_connected():
            # Try to connect
            guild = bot.get_guild(GUILD_ID)
            if guild:
                channel = bot.get_channel(VOICE_CHANNEL_ID)
                if channel:
                    voice = await channel.connect()
        
        if voice:
            # Use macOS say command to generate speech
            audio_file = '/tmp/tts_speech.aiff'
            subprocess.run(['say', '-o', audio_file, text], check=True)
            
            # Play audio
            source = discord.FFmpegPCMAudio(audio_file)
            voice.play(source)
            
            # Wait for playback
            while voice.is_playing():
                await asyncio.sleep(0.1)
            
            # Cleanup
            if os.path.exists(audio_file):
                os.remove(audio_file)
    except Exception as e:
        print(f'TTS Error: {e}')

@bot.command()
async def speak(ctx, *, text: str):
    """!speak hello world"""
    await ctx.send(f'🔊 Speaking: "{text}"')
    await speak_in_voice(text)

@bot.command()
async def join(ctx):
    """Join voice channel"""
    if ctx.author.voice and ctx.author.voice.channel:
        await ctx.author.voice.channel.connect()
        await ctx.send('✅ Joined voice channel')

@bot.command()
async def leave(ctx):
    """Leave voice channel"""
    voice = discord.utils.get(bot.voice_clients, guild__id=GUILD_ID)
    if voice:
        await voice.disconnect()
        await ctx.send('👋 Left voice channel')

bot.run(os.getenv('DISCORD_BOT_TOKEN'))
