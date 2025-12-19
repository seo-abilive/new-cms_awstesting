import { Breadcrumb as FBreadcrumb, BreadcrumbItem as FBreadcrumbItem } from 'flowbite-react'
import { useNavigation } from '@/utils/hooks/useNavigation'

/**
 * Breadcrumb component re-exported from flowbite-react.
 *
 * @see {@link https://flowbite-react.com/docs/components/breadcrumb Flowbite Breadcrumb Docs}
 * @type {import('flowbite-react').Breadcrumb}
 */
export const Breadcrumb = FBreadcrumb

/**
 * BreadcrumbItem component re-exported from flowbite-react.
 *
 * @see {@link https://flowbite-react.com/docs/components/breadcrumb Flowbite Breadcrumb Docs}
 * @type {import('flowbite-react').BreadcrumbItem}
 */
export const BreadcrumbItem = FBreadcrumbItem

/**
 * BreadNavigation component renders clickable breadcrumb items based on the provided array.
 *
 * @param {object} props
 * @param {Array<{ name: string, path?: string }>} props.breads - Breadcrumb items with names and optional paths.
 * @returns {JSX.Element}
 */
export const BreadNavigation = ({ breads = [] }) => {
    const { navigateTo } = useNavigation()
    return (
        <Breadcrumb>
            {breads.map((item, index) => {
                let hasPath = typeof item.path !== 'undefined'
                return (
                    <BreadcrumbItem
                        key={index}
                        onClick={() => {
                            if (hasPath) {
                                navigateTo(item.path)
                            }
                        }}
                        style={hasPath ? { cursor: 'pointer' } : {}}
                    >
                        {item.name}
                    </BreadcrumbItem>
                )
            })}
        </Breadcrumb>
    )
}
