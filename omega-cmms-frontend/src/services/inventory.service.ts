import { apiClient } from '@/lib/api/client';
import { ApiResponse } from '@/types';

export class InventoryService {
  async getLowStockItems(params?: any): Promise<ApiResponse<any[]>> {
    const response = await apiClient.get<ApiResponse<any[]>>('/inventory/low-stock', { params });
    return response.data;
  }
}

export const inventoryService = new InventoryService();