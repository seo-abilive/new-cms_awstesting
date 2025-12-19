import { ResourceSort } from '@/utils/components/common/ResourceSort'
import { getFileBytes, isImage } from '@/utils/common'
import { HiOutlineDocument, HiOutlinePhotograph } from 'react-icons/hi'
import dayjs from 'dayjs'
import { useModPage } from '../utils/context/ModPageContext'

export const Sort = () => {
    const { config } = useModPage()
    const breads = [{ name: config.name, path: config.path }, { name: '並び替え' }]

    const columns = [
        { key: 'file', label: '', sortable: false, _props: { style: { width: '80px' } } },
        { key: 'file_name', label: 'ファイル名' },
        { key: 'ref', label: '参照', sortable: false, _props: { style: { width: '8%' } } },
        { key: 'mime_type', label: 'ファイル形式', _props: { style: { width: '8%' } } },
        { key: 'file_size', label: 'サイズ', _props: { style: { width: '10%' } } },
        { key: 'created_at', label: '作成日', _props: { style: { width: '15%' } } },
    ]

    // ファイルアイコンを取得する関数
    const getFileIcon = (mimeType) => {
        if (isImage(mimeType)) {
            return <HiOutlinePhotograph className="w-6 h-6 text-blue-500" />
        }
        return <HiOutlineDocument className="w-6 h-6 text-gray-500" />
    }

    return (
        <ResourceSort
            options={{
                breads,
                config,
                columns,
                addScopedColumns: {
                    file: (item, row, idx) => {
                        return (
                            <td key={idx} className="text-center p-2">
                                {isImage(item.mime_type) ? (
                                    <div className="flex justify-center">
                                        <img
                                            src={item.file_url}
                                            alt={item.file_name}
                                            className="w-12 h-12 object-cover rounded border"
                                            onError={(e) => {
                                                // 画像読み込みエラー時はアイコンを表示
                                                e.target.style.display = 'none'
                                                e.target.nextSibling.style.display = 'flex'
                                            }}
                                        />
                                        <div
                                            className="w-12 h-12 flex items-center justify-center bg-gray-100 rounded border"
                                            style={{ display: 'none' }}
                                        >
                                            {getFileIcon(item.mime_type)}
                                        </div>
                                    </div>
                                ) : (
                                    <div className="flex justify-center">
                                        {getFileIcon(item.mime_type)}
                                    </div>
                                )}
                            </td>
                        )
                    },
                    file_name: (item, row, idx) => {
                        return (
                            <td>
                                <a href={item.file_url} target="_blank" rel="noopener noreferrer">
                                    {item.file_name}
                                </a>
                            </td>
                        )
                    },
                    ref: (item, row, idx) => {
                        const refs = item.content_values || item.contentValues || []
                        const count = Array.isArray(refs) ? refs.length : 0
                        return (
                            <td className="text-center" key={idx}>
                                参照 {count}
                            </td>
                        )
                    },
                    file_size: (item, row, idx) => {
                        return (
                            <td className="text-end pe-2" key={idx}>
                                {getFileBytes(item.file_size)}
                            </td>
                        )
                    },
                    created_at: (item) => {
                        return (
                            <td className="text-center">
                                {dayjs(item.created_at).format('YYYY/MM/DD HH:mm:ss')}
                            </td>
                        )
                    },
                },
            }}
        />
    )
}
