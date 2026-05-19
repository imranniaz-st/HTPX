# Advanced Server Management System - Add New Server Guide

This guide walks you through adding servers from various providers to your Advanced Server Management System.

## Table of Contents
- [Quick Start](#quick-start)
- [Hostinger VPS](#hostinger-vps)
- [AWS EC2](#aws-ec2)
- [DigitalOcean](#digitalocean)
- [Linode](#linode)
- [Self-Hosted/On-Premises](#self-hosted-on-premises)
- [Generic Linux Server](#generic-linux-server)
- [Firewall Configuration](#firewall-configuration)
- [SSH Key Setup](#ssh-key-setup)
- [Troubleshooting](#troubleshooting)

---

## Quick Start

To add a server to the system, you need:
1. **SSH Access**: Server hostname/IP and SSH credentials
2. **Port 22**: SSH port open (can be changed)
3. **A User Account**: With password change privileges (optional)

### Add Server via Dashboard

1. Go to **Servers** page
2. Click **+ Add Server**
3. Fill in:
   - **Name**: Descriptive name (e.g., "Hostinger-Web-01")
   - **IP Address**: Public IP or hostname
   - **SSH Port**: Usually 22
   - **SSH Username**: Usually `root` or your user
   - **SSH Password** or **Private Key**: Authentication method
   - **Hostname**: Server's hostname (optional)
   - **OS Type**: Linux distribution (Ubuntu, CentOS, etc.)
4. Click **Add Server**
5. Test connection before saving

---

## Hostinger VPS

Hostinger is a popular hosting provider with affordable VPS options. Here's how to set up SSH access.

### Step 1: Generate SSH Key Pair (Recommended)

On your local machine (Windows, Mac, or Linux), generate SSH keys:

**On Windows (using Git Bash, PowerShell, or WSL):**
```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/hostinger_key
```

**On Mac/Linux:**
```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/hostinger_key
```

When prompted, press Enter to skip passphrase (or set one for security).

This creates two files:
- `~/.ssh/hostinger_key` (private key - keep secret)
- `~/.ssh/hostinger_key.pub` (public key - upload to server)

### Step 2: Access Hostinger Control Panel

1. Log in to [Hostinger Dashboard](https://hpanel.hostinger.com)
2. Go to **VPS** → Select your VPS
3. Click **Manage**

### Step 3: Configure SSH Access

**Option A: Using Password (Simpler but less secure)**

1. Click **Security** or **SSH**
2. Note your:
   - Server IP Address
   - SSH Port (usually 22, might be custom like 2222)
   - Root Password (or reset if needed)

**Option B: Using SSH Key (Recommended)**

1. Go to **SSH Keys** in Hostinger control panel
2. Click **Add SSH Key**
3. Paste your public key content (from `hostinger_key.pub`)
4. Name it (e.g., "Local Machine")
5. Save
6. Note the SSH port and IP address

### Step 4: Test SSH Connection from Local Machine

```bash
# Using password
ssh -p 22 root@YOUR_HOSTINGER_IP

# Using SSH key
ssh -i ~/.ssh/hostinger_key -p 22 root@YOUR_HOSTINGER_IP
```

If it works, you'll see the server prompt.

### Step 5: Add Server to Management System

In your dashboard:

1. **Name**: `Hostinger-Web-01` (or your choice)
2. **IP Address**: `123.456.789.012` (your Hostinger IP)
3. **SSH Port**: `22` (or custom port from Hostinger)
4. **SSH Username**: `root` (or your non-root user if configured)
5. **Authentication**:
   - Password: Enter your Hostinger root password
   - OR Private Key: Paste contents of `hostinger_key` file
6. **OS Type**: Select your OS (Ubuntu 22.04, CentOS, etc.)
7. Click **Test Connection** → Should see "Connected"
8. Click **Add Server**

### Step 6: Configure Firewall on Hostinger Server

```bash
# Enable UFW firewall (on Ubuntu)
sudo ufw enable

# Allow SSH (so you don't get locked out)
sudo ufw allow 22/tcp
sudo ufw allow ssh

# View status
sudo ufw status
```

---

## AWS EC2

AWS EC2 is a cloud service with flexible pricing and powerful options.

### Step 1: Launch EC2 Instance

1. Go to [AWS Console](https://console.aws.amazon.com)
2. Navigate to **EC2** → **Instances**
3. Click **Launch Instance**
4. Select Ubuntu 22.04 LTS or your preferred image
5. Choose instance type (t3.micro for free tier)
6. Click **Next**

### Step 2: Create/Configure Key Pair

On the **Key Pair** step:

**New Key Pair:**
1. Click **Create New Key Pair**
2. Name: `aws-management-key`
3. Type: `RSA`
4. Format: `.pem` (for Mac/Linux) or `.ppk` (for PuTTY on Windows)
5. Download the key - **Keep it safe!**

### Step 3: Configure Security Group

1. Create new security group
2. **Inbound Rules**:
   - SSH (22) from your IP or 0.0.0.0/0 (less secure)
   - HTTP (80) if needed
   - HTTPS (443) if needed

3. Click **Launch Instance**

### Step 4: Get Instance Details

1. After launch, go to **Instances**
2. Select your instance
3. Note:
   - **Public IPv4 Address** (for SSH connection)
   - **Instance ID**

### Step 5: Connect via SSH

```bash
# Make key readable
chmod 600 ~/Downloads/aws-management-key.pem

# Connect to EC2
ssh -i ~/Downloads/aws-management-key.pem ubuntu@EC2_PUBLIC_IP
```

Example:
```bash
ssh -i ~/Downloads/aws-management-key.pem ubuntu@54.123.45.678
```

### Step 6: Add to Management System

1. **Name**: `AWS-Web-Server` (or name of instance)
2. **IP Address**: Public IPv4 Address from AWS Console
3. **SSH Port**: `22`
4. **SSH Username**: `ubuntu` (default for Ubuntu AMI)
5. **SSH Key**: Paste contents of `.pem` file
6. **OS Type**: Ubuntu 22.04 LTS
7. Test and save

---

## DigitalOcean

DigitalOcean provides droplets with simple management interface.

### Step 1: Create Droplet

1. Log in to [DigitalOcean Dashboard](https://cloud.digitalocean.com)
2. Click **Create** → **Droplets**
3. Choose:
   - **Region**: Pick closest to you
   - **Image**: Ubuntu 22.04 x64
   - **Size**: Basic ($5-$6/month minimum)
4. **Authentication**: Select SSH Key

### Step 2: Add SSH Key to DigitalOcean

1. In Droplet creation page, go to **SSH keys**
2. Click **New SSH Key**
3. Paste your public key (from `~/.ssh/id_rsa.pub`)
4. Name it and save

### Step 3: Create Droplet

Click **Create Droplet** and wait 30-60 seconds for it to boot.

### Step 4: Get Droplet IP

1. New droplet appears in **Droplets** list
2. Note the **IPv4 Address**

### Step 5: Connect

```bash
# Using SSH key (recommended)
ssh -i ~/.ssh/id_rsa root@DROPLET_IP

# Example:
ssh -i ~/.ssh/id_rsa root@192.168.1.100
```

### Step 6: Add to Management System

1. **Name**: `DigitalOcean-Droplet-1`
2. **IP Address**: Droplet's IPv4
3. **SSH Port**: `22`
4. **SSH Username**: `root`
5. **SSH Key**: Paste your private key content
6. **OS Type**: Ubuntu 22.04 LTS
7. Test and save

---

## Linode

Linode offers flexible cloud infrastructure with good documentation.

### Step 1: Create Linode Instance

1. Log in to [Linode Cloud Manager](https://cloud.linode.com)
2. Click **Create** → **Linode**
3. Choose:
   - **Image**: Ubuntu 22.04 LTS
   - **Region**: Nearest to you
   - **Linode Plan**: Shared CPU (cheapest option)
4. Scroll to **SSH Key**

### Step 2: Generate SSH Key

If you don't have one, generate it first:

```bash
ssh-keygen -t rsa -b 4096 -f ~/.ssh/linode_key
```

### Step 3: Add Public Key to Linode

1. In Linode creation form, click **New SSH Key**
2. Paste public key from `~/.ssh/linode_key.pub`
3. Name: `My SSH Key`
4. Add

### Step 4: Create and Boot

1. Set **Label**: e.g., "Web Server 1"
2. Set **Root Password**: (backup, use SSH keys primarily)
3. Click **Create Linode**
4. Wait for boot (2-5 minutes)

### Step 5: Get IP Address

1. Go to **Linodes**
2. Click your new Linode
3. Note the **IPv4 Address**

### Step 6: SSH Connection

```bash
ssh -i ~/.ssh/linode_key root@LINODE_IP
```

### Step 7: Add to Management System

1. **Name**: `Linode-Server-1`
2. **IP Address**: Linode's IPv4
3. **SSH Port**: `22`
4. **SSH Username**: `root`
5. **SSH Key**: Paste `linode_key` private key
6. **OS Type**: Ubuntu 22.04 LTS
7. Test and save

---

## Self-Hosted/On-Premises

Managing your own physical or virtual servers in your infrastructure.

### Step 1: Enable SSH on Your Server

**If SSH is not installed:**

```bash
# On Ubuntu/Debian
sudo apt-get update
sudo apt-get install openssh-server openssh-client

# On CentOS/RHEL
sudo yum install openssh-server openssh-clients

# Start SSH service
sudo systemctl start ssh
sudo systemctl enable ssh  # Auto-start on boot
```

### Step 2: Configure SSH

Edit `/etc/ssh/sshd_config`:

```bash
sudo nano /etc/ssh/sshd_config
```

Recommended settings:

```conf
# Change port (optional, increases security)
Port 22

# Root login (disable in production)
PermitRootLogin yes

# Password authentication (use keys instead)
PasswordAuthentication yes
PubkeyAuthentication yes

# Keep alive
ClientAliveInterval 300
ClientAliveCountMax 2
```

Restart SSH:
```bash
sudo systemctl restart ssh
```

### Step 3: Get Server IP Address

```bash
# Find IP address
hostname -I
ip addr show
```

### Step 4: Test Connection from Network

```bash
ssh username@YOUR_SERVER_IP
```

### Step 5: Configure Firewall

```bash
# Enable firewall
sudo ufw enable

# Allow SSH
sudo ufw allow 22/tcp

# Check status
sudo ufw status
```

### Step 6: Add to Management System

1. **Name**: `On-Prem-Server-01`
2. **IP Address**: Your server's local or public IP
3. **SSH Port**: `22` (or custom)
4. **SSH Username**: Your username
5. **Authentication**: Password or SSH Key
6. **OS Type**: Your Linux distribution
7. Test and save

---

## Generic Linux Server

For any Linux server you have SSH access to:

### Minimum Requirements

- Linux OS (Ubuntu, CentOS, Debian, etc.)
- SSH service running
- SSH access (password or key-based)
- Port 22 open (or custom port)

### Quick Checklist

- [ ] Server has public/accessible IP address
- [ ] SSH port is open to your network
- [ ] You have SSH credentials (password or key)
- [ ] You can SSH in manually: `ssh user@ip`

### Add to System

1. **Name**: Descriptive name
2. **IP Address**: Server's IP or hostname
3. **SSH Port**: Usually 22
4. **SSH Username**: Your login user
5. **Authentication**: 
   - **Password**: Your login password (less secure)
   - **Private Key**: SSH private key content (recommended)
6. **OS Type**: Ubuntu, CentOS, Debian, etc.
7. Click **Test Connection**
8. If successful, click **Add Server**

---

## Firewall Configuration

### Inbound Rules (What's allowed INTO server)

After adding server, configure what traffic is allowed:

1. Go to **Servers** → Your Server → **Firewall** tab
2. Click **+ Add Rule**
3. Examples:

**Allow SSH from your IP:**
- Direction: Inbound
- Protocol: TCP
- Port: 22
- Source: YOUR_IP (find at whatismyipaddress.com)
- Action: Allow

**Allow HTTP/HTTPS for Web Server:**
- Direction: Inbound
- Protocol: TCP
- Port: 80
- Source: 0.0.0.0/0 (anywhere)
- Action: Allow

- Direction: Inbound
- Protocol: TCP
- Port: 443
- Source: 0.0.0.0/0
- Action: Allow

### Outbound Rules (What's allowed OUT of server)

**Allow DNS (required):**
- Direction: Outbound
- Protocol: UDP
- Port: 53
- Destination: 0.0.0.0/0
- Action: Allow

**Allow HTTP/HTTPS outbound:**
- Direction: Outbound
- Protocol: TCP
- Port: 80, 443
- Destination: 0.0.0.0/0
- Action: Allow

---

## SSH Key Setup

### Generate SSH Key Pair (One-Time)

```bash
# Generate key
ssh-keygen -t rsa -b 4096 -f ~/.ssh/id_rsa

# View public key (copy and paste to server)
cat ~/.ssh/id_rsa.pub
```

### Add Public Key to Server

**Method 1: Via ssh-copy-id (Easy)**

```bash
ssh-copy-id -i ~/.ssh/id_rsa user@SERVER_IP
```

**Method 2: Manual (SSH in first)**

```bash
# SSH into server with password
ssh user@SERVER_IP

# Create .ssh directory
mkdir -p ~/.ssh
chmod 700 ~/.ssh

# Add your public key
echo "YOUR_PUBLIC_KEY_HERE" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
exit
```

**Method 3: For Hostinger/AWS/DigitalOcean**

Upload to provider's control panel (see respective sections above).

### Test SSH Key Authentication

```bash
ssh -i ~/.ssh/id_rsa user@SERVER_IP
```

Should connect without password prompt.

---

## Troubleshooting

### "Connection Refused"

**Problem**: Can't connect to server SSH
**Solutions:**
1. Check IP address is correct
2. Verify SSH port is open: `nmap -p 22 SERVER_IP`
3. SSH service not running: `sudo systemctl status ssh`
4. Firewall blocking: `sudo ufw allow 22/tcp`

### "Permission Denied (publickey)"

**Problem**: SSH key authentication failing
**Solutions:**
1. Check permissions: `chmod 600 ~/.ssh/id_rsa`
2. Verify public key on server: `cat ~/.ssh/authorized_keys`
3. Check SSH config: `cat /etc/ssh/sshd_config` for `PubkeyAuthentication yes`

### "Host key verification failed"

**Problem**: First SSH connection prompts about host key
**Solution**: This is normal on first connection
```bash
# Type 'yes' when prompted to add server to known_hosts
ssh user@SERVER_IP
```

### Firewall Rules Not Working

**Problem**: Rules configured but traffic still blocked
**Solutions:**
1. Check provider's firewall (AWS Security Group, Linode Firewall)
2. Apply system firewall: `sudo ufw enable` and `sudo ufw reload`
3. Check iptables: `sudo iptables -L`

### "Too many authentication failures"

**Problem**: Locked out after many failed attempts
**Solution:**
1. Wait 10-15 minutes
2. Try from different IP
3. Use correct credentials
4. Check SSH logs: `sudo tail -f /var/log/auth.log`

### Can't Change Server Password

**Problem**: Password change operation failing
**Possible causes:**
- SSH user doesn't have sudo/root access
- Current password incorrect
- Server doesn't allow password changes for security reasons
- Password complexity requirements not met

**Solution:**
1. Ensure user has sudo access: `sudo visudo`
2. Check user can run: `sudo passwd`
3. Verify password meets complexity (8+ chars, uppercase, numbers)

---

## Best Practices

DO:
- Use SSH keys instead of passwords
- Restrict SSH to specific IPs when possible
- Change default SSH port from 22 (optional)
- Keep servers updated: `sudo apt update && sudo apt upgrade`
- Use strong passwords when required
- Enable 2FA if provider supports it
- Monitor logs regularly

DON'T:
- Share SSH private keys
- Use password authentication (keys are better)
- Allow root SSH login in production
- Leave firewall ports wide open (0.0.0.0/0) unnecessarily
- Use same credentials for multiple servers
- Ignore security updates

---

## Need Help?

For provider-specific issues:
- **Hostinger**: https://support.hostinger.com
- **AWS**: https://aws.amazon.com/support
- **DigitalOcean**: https://www.digitalocean.com/docs
- **Linode**: https://www.linode.com/docs

For management system issues, check the main README or contact support.
