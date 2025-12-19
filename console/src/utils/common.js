import qs from 'qs'
import { HiOutlineDocument, HiOutlinePhotograph } from 'react-icons/hi'

export const getUrlParams = (params = {}) => {
    return qs.stringify(params)
}

export const getChoice = (choices = [], value) => {
    let choice = choices.find((item) => item.value === value)
    return choice
}

export const isImage = (mimeType) => {
    return mimeType && mimeType.startsWith('image/')
}

// ファイル名の拡張子を取得する関数
export const getFileExtension = (fileName) => {
    return fileName.split('.').pop().toLowerCase()
}

// ファイルサイズを取得する関数
export const getFileBytes = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
