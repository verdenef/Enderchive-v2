import axios from 'axios'

/**
 * API composable for authenticated requests
 */
export function useApi() {
  const getToken = () => {
    if (typeof window === 'undefined') return null
    return localStorage.getItem('api_token')
  }

  const setToken = (token) => {
    if (typeof window === 'undefined') return
    if (token) {
      localStorage.setItem('api_token', token)
    } else {
      localStorage.removeItem('api_token')
    }
  }

  const getAuthHeaders = () => {
    const token = getToken()
    return token ? { Authorization: `Bearer ${token}` } : {}
  }

  const apiRequest = async (method, url, data = null, config = {}) => {
    const headers = {
      ...getAuthHeaders(),
      ...(config.headers || {})
    }

    const requestConfig = {
      ...config,
      headers
    }

    switch (method.toLowerCase()) {
      case 'get':
        return axios.get(url, requestConfig)
      case 'post':
        return axios.post(url, data, requestConfig)
      case 'put':
        return axios.put(url, data, requestConfig)
      case 'patch':
        return axios.patch(url, data, requestConfig)
      case 'delete':
        return axios.delete(url, requestConfig)
      default:
        throw new Error(`Unsupported HTTP method: ${method}`)
    }
  }

  return {
    getToken,
    setToken,
    getAuthHeaders,
    apiRequest
  }
}

