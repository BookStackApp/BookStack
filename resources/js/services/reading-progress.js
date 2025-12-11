import http from './http';

/**
 * Service for managing reading progress data
 */
class ReadingProgressService {
    /**
     * Get reading progress for a specific page
     * @param {number} pageId
     * @returns {Promise<Object>}
     */
    async getProgress(pageId) {
        try {
            const response = await http.get(`/api/pages/${pageId}/reading-progress`);
            return response.data;
        } catch (error) {
            console.error('Failed to get reading progress:', error);
            throw error;
        }
    }

    /**
     * Update reading progress for a page
     * @param {number} pageId
     * @param {Object} progressData
     * @param {number} progressData.progress_percentage
     * @param {number} progressData.scroll_position
     * @param {number} progressData.time_spent_seconds
     * @param {boolean} progressData.is_completed
     * @returns {Promise<Object>}
     */
    async updateProgress(pageId, progressData) {
        try {
            const response = await http.put(`/api/pages/${pageId}/reading-progress`, progressData);
            return response.data;
        } catch (error) {
            console.error('Failed to update reading progress:', error);
            throw error;
        }
    }

    /**
     * Delete reading progress for a page
     * @param {number} pageId
     * @returns {Promise<Object>}
     */
    async deleteProgress(pageId) {
        try {
            const response = await http.delete(`/api/pages/${pageId}/reading-progress`);
            return response.data;
        } catch (error) {
            console.error('Failed to delete reading progress:', error);
            throw error;
        }
    }

    /**
     * Get reading statistics for current user
     * @returns {Promise<Object>}
     */
    async getUserStats() {
        try {
            const response = await http.get('/api/users/me/reading-stats');
            return response.data.statistics;
        } catch (error) {
            console.error('Failed to get user reading stats:', error);
            throw error;
        }
    }

    /**
     * Get all reading progress for current user
     * @param {number} limit
     * @returns {Promise<Array>}
     */
    async getUserProgress(limit = 50) {
        try {
            const response = await http.get(`/api/users/me/reading-progress?limit=${limit}`);
            return response.data.data;
        } catch (error) {
            console.error('Failed to get user reading progress:', error);
            throw error;
        }
    }

    /**
     * Batch update reading progress for multiple pages
     * @param {Array} progressItems
     * @returns {Promise<Array>}
     */
    async batchUpdateProgress(progressItems) {
        const promises = progressItems.map(item => 
            this.updateProgress(item.page_id, {
                progress_percentage: item.progress_percentage,
                scroll_position: item.scroll_position,
                time_spent_seconds: item.time_spent_seconds,
                is_completed: item.is_completed
            })
        );

        try {
            return await Promise.all(promises);
        } catch (error) {
            console.error('Failed to batch update reading progress:', error);
            throw error;
        }
    }
}

export default new ReadingProgressService();