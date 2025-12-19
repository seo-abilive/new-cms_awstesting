import { ResourceForm } from '@/utils/components/common/ResourceForm'
import { useParams } from 'react-router'
import { HiOutlineDocument, HiOutlineExternalLink, HiOutlinePhotograph } from 'react-icons/hi'
import { getFileBytes, getFileExtension, isImage } from '@/utils/common'
import { useModPage } from '../utils/context/ModPageContext'

const Form = ({ pageName }) => {
    const { id } = useParams()
    const { config } = useModPage()
    const breads = [{ name: config.name, path: config.path }, { name: pageName }]

    // ファイルアイコンを取得する関数
    const getFileIcon = (mimeType) => {
        if (isImage(mimeType)) {
            return <HiOutlinePhotograph className="w-6 h-6 text-blue-500" />
        }
        return <HiOutlineDocument className="w-6 h-6 text-gray-500" />
    }

    const formItem = [
        {
            title: 'ファイル',
            id: 'file',
            formType: 'component',
            onFetch: (value, data) => {
                return data
            },
            component: ({ defaultValue, ...props }) => {
                if (!defaultValue || !defaultValue.file_url) {
                    return <div className="text-gray-500 text-sm">ファイルが選択されていません</div>
                }

                return (
                    <div className="flex items-center space-x-4 p-4 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-800">
                        {isImage(defaultValue.mime_type) ? (
                            // 画像の場合
                            <div className="flex items-center space-x-4">
                                <img
                                    src={defaultValue.file_url}
                                    alt={defaultValue.file_name}
                                    onClick={() => window.open(defaultValue.file_url, '_blank')}
                                    className="w-16 h-16 object-cover rounded border cursor-pointer hover:opacity-80 transition-opacity"
                                />
                                <div
                                    className="w-16 h-16 flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded border cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
                                    style={{ display: 'none' }}
                                    onClick={() => window.open(defaultValue.file_url, '_blank')}
                                >
                                    {getFileIcon(defaultValue.mime_type)}
                                </div>
                                <div className="flex-1">
                                    <div className="font-medium text-gray-900 dark:text-gray-100">
                                        {defaultValue.file_name}
                                    </div>
                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                        {defaultValue.mime_type} •{' '}
                                        {defaultValue.file_size
                                            ? Math.round(defaultValue.file_size / 1024) + ' KB'
                                            : 'Unknown size'}
                                    </div>
                                </div>
                            </div>
                        ) : (
                            // 画像以外の場合
                            <div className="flex items-center space-x-4">
                                <a
                                    href={defaultValue.file_url}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="flex flex-col items-center justify-center w-16 h-16 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded border transition-colors group"
                                    title={`${defaultValue.file_name} (${getFileExtension(
                                        defaultValue.file_name
                                    ).toUpperCase()})`}
                                >
                                    {getFileIcon(defaultValue.mime_type)}
                                    <HiOutlineExternalLink className="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 mt-1" />
                                </a>
                                <div className="flex-1">
                                    <div className="font-medium text-gray-900 dark:text-gray-100">
                                        {defaultValue.file_name}
                                    </div>
                                    <div className="text-sm text-gray-500 dark:text-gray-400">
                                        {defaultValue.mime_type} •{' '}
                                        {getFileBytes(defaultValue.file_size)}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                )
            },
        },
        { title: 'ファイル名', id: 'file_name', required: true },
        { title: '代替テキスト', id: 'alt_text' },
    ]

    return (
        <>
            <ResourceForm options={{ breads, config, formItem, id }} />
        </>
    )
}

export const Edit = () => {
    return (
        <>
            <Form pageName={'編集'} />
        </>
    )
}
