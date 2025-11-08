// Configuration
const API_BASE_URL = 'http://localhost/UniVerse-Events/backend/api';

class AppState {
    constructor() {
        this.currentUser = null;
        this.events = [];
        this.init();
    }

    async init() {
        await this.checkAuth();
        await this.loadEvents();
        this.updateUI();
    }

    async checkAuth() {
        try {
            const response = await fetch(`${API_BASE_URL}/auth.php?action=check`, {
                credentials: 'include'
            });
            const data = await response.json();
            
            if (data.authenticated) {
                this.currentUser = data.user;
            }
            return data;
        } catch (error) {
            console.error('Auth check failed:', error);
            return { authenticated: false };
        }
    }

    async loadEvents() {
        try {
            const response = await fetch(`${API_BASE_URL}/events.php`);
            const data = await response.json();
            
            if (data.success) {
                this.events = data.events;
                this.renderEvents();
            }
        } catch (error) {
            console.error('Failed to load events:', error);
        }
    }

    renderEvents() {
        const container = document.getElementById('eventsContainer');
        if (!container) return;

        container.innerHTML = this.events.map(event => `
            <div class="card">
                <div class="event-content">
                    <h3>${this.escapeHtml(event.title)}</h3>
                    <p>${this.escapeHtml(event.description)}</p>
                    <div style="display: flex; justify-content: space-between; margin-top: 1rem;">
                        <span>📅 ${new Date(event.date).toLocaleDateString()}</span>
                        <span>📍 ${this.escapeHtml(event.venue)}</span>
                    </div>
                    <a href="events.html" class="btn btn-primary" style="width: 100%; margin-top: 1rem;">
                        View Details
                    </a>
                </div>
            </div>
        `).join('');
    }

    updateUI() {
        const authElements = document.querySelectorAll('.auth-only');
        const guestElements = document.querySelectorAll('.guest-only');

        if (this.currentUser) {
            authElements.forEach(el => el.style.display = 'block');
            guestElements.forEach(el => el.style.display = 'none');
        } else {
            authElements.forEach(el => el.style.display = 'none');
            guestElements.forEach(el => el.style.display = 'block');
        }
    }

    escapeHtml(unsafe) {
        return unsafe
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
}

const app = new AppState();