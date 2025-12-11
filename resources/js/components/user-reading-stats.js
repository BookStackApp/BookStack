import {defineComponent} from 'vue';
import readingProgressService from '../services/reading-progress.js';

export default defineComponent({
    name: 'UserReadingStats',
    props: {
        userId: {
            type: Number,
            required: true
        },
        compact: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            stats: null,
            recentProgress: [],
            loading: true,
            error: null,
            timeRange: '7d',
            availableRanges: [
                { value: '1d', label: '今天' },
                { value: '7d', label: '本周' },
                { value: '30d', label: '本月' },
                { value: '90d', label: '本季度' },
                { value: '365d', label: '本年' },
                { value: 'all', label: '全部' }
            ]
        };
    },
    computed: {
        formattedStats() {
            if (!this.stats) return null;
            
            return {
                totalPages: this.stats.total_pages || 0,
                completedPages: this.stats.completed_pages || 0,
                totalReadingTime: this.formatDuration(this.stats.total_reading_time_seconds || 0),
                averageReadingTime: this.formatDuration(this.stats.average_reading_time_seconds || 0),
                completionRate: this.stats.completion_rate || 0,
                streakDays: this.stats.streak_days || 0,
                favoriteBook: this.stats.favorite_book || null,
                favoriteChapter: this.stats.favorite_chapter || null
            };
        },
        readingProgressChart() {
            if (!this.stats) return [];
            
            return [
                {
                    label: '已完成',
                    value: this.stats.completed_pages || 0,
                    color: '#10b981'
                },
                {
                    label: '阅读中',
                    value: (this.stats.total_pages || 0) - (this.stats.completed_pages || 0),
                    color: '#3b82f6'
                }
            ];
        },
        recentActivity() {
            if (!this.recentProgress || this.recentProgress.length === 0) return [];
            
            return this.recentProgress.slice(0, 5).map(item => ({
                pageTitle: item.page_title || '未命名页面',
                bookTitle: item.book_title || '未命名书籍',
                progress: item.progress_percentage || 0,
                lastRead: this.formatRelativeTime(item.last_read_at),
                isCompleted: item.is_completed || false,
                readingTime: this.formatDuration(item.time_spent_seconds || 0)
            }));
        }
    },
    async mounted() {
        await this.loadStats();
    },
    watch: {
        timeRange() {
            this.loadStats();
        }
    },
    methods: {
        async loadStats() {
            this.loading = true;
            this.error = null;
            
            try {
                const [stats, progress] = await Promise.all([
                    readingProgressService.getUserStats(this.timeRange),
                    readingProgressService.getUserProgress()
                ]);
                
                this.stats = stats;
                this.recentProgress = progress || [];
            } catch (error) {
                console.error('Failed to load reading stats:', error);
                this.error = error.message || '加载阅读统计失败';
            } finally {
                this.loading = false;
            }
        },
        
        formatDuration(seconds) {
            if (seconds < 60) return `${Math.round(seconds)}秒`;
            if (seconds < 3600) return `${Math.round(seconds / 60)}分钟`;
            if (seconds < 86400) return `${Math.round(seconds / 3600)}小时`;
            return `${Math.round(seconds / 86400)}天`;
        },
        
        formatRelativeTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.round(diffMs / 60000);
            
            if (diffMins < 1) return '刚刚';
            if (diffMins < 60) return `${diffMins}分钟前`;
            if (diffMins < 1440) return `${Math.round(diffMins / 60)}小时前`;
            if (diffMins < 10080) return `${Math.round(diffMins / 1440)}天前`;
            
            return date.toLocaleDateString('zh-CN');
        },
        
        getProgressColor(progress) {
            if (progress >= 100) return '#10b981';
            if (progress >= 75) return '#3b82f6';
            if (progress >= 50) return '#8b5cf6';
            if (progress >= 25) return '#f59e0b';
            return '#ef4444';
        },
        
        getProgressLabel(progress) {
            if (progress >= 100) return '已完成';
            if (progress >= 75) return '即将完成';
            if (progress >= 50) return '阅读中';
            if (progress >= 25) return '开始阅读';
            return '刚开始';
        },
        
        refreshStats() {
            this.loadStats();
        }
    },
    template: `
        <div class="user-reading-stats" :class="{ 'compact': compact }">
            <div v-if="loading" class="loading-state">
                <div class="loading-spinner"></div>
                <p>加载阅读统计中...</p>
            </div>
            
            <div v-else-if="error" class="error-state">
                <i class="icon icon-warning"></i>
                <p>{{ error }}</p>
                <button @click="refreshStats" class="btn btn-small">重试</button>
            </div>
            
            <div v-else-if="formattedStats" class="stats-content">
                <div class="stats-header">
                    <h3>阅读统计</h3>
                    <div class="time-range-selector">
                        <select v-model="timeRange" class="input-small">
                            <option v-for="range in availableRanges" :key="range.value" :value="range.value">
                                {{ range.label }}
                            </option>
                        </select>
                        <button @click="refreshStats" class="btn btn-small btn-icon" title="刷新">
                            <i class="icon icon-refresh"></i>
                        </button>
                    </div>
                </div>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="icon icon-book-open"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ formattedStats.totalPages }}</div>
                            <div class="stat-label">总阅读页面</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="icon icon-check-circle"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ formattedStats.completedPages }}</div>
                            <div class="stat-label">已完成页面</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="icon icon-clock"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ formattedStats.totalReadingTime }}</div>
                            <div class="stat-label">总阅读时间</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="icon icon-trending-up"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ formattedStats.completionRate }}%</div>
                            <div class="stat-label">完成率</div>
                        </div>
                    </div>
                </div>
                
                <div class="stats-details">
                    <div class="detail-section">
                        <h4>阅读习惯</h4>
                        <div class="detail-items">
                            <div class="detail-item">
                                <span class="detail-label">平均阅读时间</span>
                                <span class="detail-value">{{ formattedStats.averageReadingTime }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">连续阅读天数</span>
                                <span class="detail-value">{{ formattedStats.streakDays }}天</span>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="formattedStats.favoriteBook" class="detail-section">
                        <h4>最爱书籍</h4>
                        <div class="detail-items">
                            <div class="detail-item">
                                <span class="detail-label">书名</span>
                                <span class="detail-value">{{ formattedStats.favoriteBook.title }}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">阅读进度</span>
                                <span class="detail-value">{{ formattedStats.favoriteBook.progress }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div v-if="recentActivity.length > 0" class="recent-activity">
                    <h4>最近阅读</h4>
                    <div class="activity-list">
                        <div v-for="activity in recentActivity" :key="activity.pageTitle" class="activity-item">
                            <div class="activity-info">
                                <div class="activity-title">{{ activity.pageTitle }}</div>
                                <div class="activity-book">{{ activity.bookTitle }}</div>
                            </div>
                            <div class="activity-meta">
                                <div class="activity-progress">
                                    <div class="progress-bar">
                                        <div 
                                            class="progress-fill" 
                                            :style="{ width: activity.progress + '%', backgroundColor: getProgressColor(activity.progress) }"
                                        ></div>
                                    </div>
                                    <span>{{ getProgressLabel(activity.progress) }}</span>
                                </div>
                                <div class="activity-time">{{ activity.lastRead }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `
});