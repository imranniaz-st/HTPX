# Frontend Setup Instructions

## System Requirements

- Node.js 18+
- npm or yarn
- Vite 4.0+

## Installation

### 1. Install Dependencies

```bash
cd frontend
npm install
```

### 2. Environment Configuration

Create `.env.local`:

```env
VITE_API_BASE_URL=http://localhost:8000/api
VITE_PUSHER_APP_KEY=your_pusher_key
VITE_PUSHER_HOST=localhost
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
VITE_PUSHER_CLUSTER=mt1
```

### 3. Development Server

```bash
npm run dev
```

Frontend will be available at `http://localhost:5173`

### 4. Build for Production

```bash
npm run build
```

Compiled files will be in the `dist/` directory.

## Available Scripts

```bash
# Start development server
npm run dev

# Build for production
npm run build

# Preview production build locally
npm run preview

# Lint and fix code
npm run lint
```

## Project Structure

```
frontend/
├── src/
│   ├── components/      # Reusable Vue components
│   ├── views/           # Page components
│   ├── stores/          # Pinia state management
│   ├── services/        # API client services
│   ├── styles/          # CSS and Tailwind
│   ├── router/          # Vue Router configuration
│   ├── App.vue          # Root component
│   └── main.js          # Entry point
├── public/              # Static assets
├── index.html           # HTML template
├── vite.config.js       # Vite configuration
├── tailwind.config.js   # Tailwind CSS configuration
└── package.json         # Dependencies
```

## Components

### Dashboard

- Real-time server statistics
- Recent alerts display
- System metrics overview

### Servers

- List all managed servers
- Add new servers
- View server details
- Edit server information
- Monitor real-time metrics

### Alerts

- View all system alerts
- Filter by severity/status
- Resolve alerts
- Alert rules management

### Firewall

- Manage firewall rules
- Configure inbound/outbound rules
- Enable/disable rules

### User Management

- Change server user passwords
- Manage user permissions
- View user list per server

## API Integration

The frontend communicates with the backend API at `http://localhost:8000/api`.

### Authentication

All requests require a Bearer token:

```javascript
Authorization: Bearer {token}
```

Tokens are stored in localStorage and automatically included in API requests.

## State Management (Pinia)

### Available Stores

- `authStore` - User authentication and authorization
- `serverStore` - Server management
- `alertStore` - Alert management

### Example Usage

```javascript
import { useServerStore } from '@/stores/serverStore'

const serverStore = useServerStore()
await serverStore.fetchServers()
console.log(serverStore.servers)
```

## Styling

Using Tailwind CSS for all styles. Custom classes defined in `src/styles/main.css`.

### Available CSS Classes

- `.btn`, `.btn-primary`, `.btn-secondary`, `.btn-danger`
- `.badge`, `.badge-success`, `.badge-warning`, `.badge-danger`
- `.card`, `.card-header`
- `.input`
- `.alert`, `.alert-success`, `.alert-warning`, `.alert-danger`

## Real-time Updates

Real-time server metrics and alerts use WebSocket connections via Pusher/Laravel Echo.

Configure in `.env.local`:

```env
VITE_PUSHER_APP_KEY=your_key
VITE_PUSHER_HOST=localhost
VITE_PUSHER_PORT=6001
```

## Browser Support

- Chrome/Edge 90+
- Firefox 88+
- Safari 14+

## Troubleshooting

### API Connection Issues

1. Check backend is running: `http://localhost:8000/health`
2. Verify `VITE_API_BASE_URL` in `.env.local`
3. Check CORS configuration in backend
4. Review browser console for errors

### Build Issues

```bash
# Clear node_modules and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Vite cache
rm -rf dist
npm run build
```

### Hot Module Replacement (HMR) Not Working

Ensure Vite server is running and check for port conflicts.

## Performance

- Code splitting on route changes
- Lazy loading of components
- Minified production builds
- Gzip compression recommended

## Security

- API token expires after inactivity
- CSRF protection enabled
- Sanitized user inputs
- Secure localStorage usage

## Contributing

1. Create a feature branch
2. Follow Vue 3 Composition API patterns
3. Use Tailwind CSS for styling
4. Write clear commit messages
5. Test before pushing

## License

MIT License

## Support

For issues or questions:
- Check [Vue 3 Documentation](https://vuejs.org/)
- Review [Vite Guide](https://vitejs.dev/)
- See project GitHub Issues
