#!/bin/bash

# Generate SSH keys for Server Manager
# Run from project root

if [ ! -d "backend/storage/ssh" ]; then
    mkdir -p backend/storage/ssh
fi

# Generate RSA key pair
ssh-keygen -t rsa -b 4096 -f backend/storage/ssh/server_manager_key -N ""

# Set proper permissions
chmod 600 backend/storage/ssh/server_manager_key
chmod 644 backend/storage/ssh/server_manager_key.pub

echo "SSH keys generated successfully!"
echo ""
echo "Private key: backend/storage/ssh/server_manager_key"
echo "Public key: backend/storage/ssh/server_manager_key.pub"
echo ""
echo "To authorize this key on your servers, add the public key to ~/.ssh/authorized_keys"
echo ""
echo "Example:"
echo "  cat backend/storage/ssh/server_manager_key.pub | ssh user@server 'cat >> ~/.ssh/authorized_keys'"
