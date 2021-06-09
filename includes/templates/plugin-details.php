<h3><b><?php echo WPPR_Name; ?> Details</b></h3>
<table class="wppr-plugin-details">
    <tbody>

        <tr>
            <th>Name</th>
            <td><a href="https://wordpress.org/plugins/<?php echo $args->slug ?? WPPR_Slug; ?>" target="_blank"><?php echo $args->name ?? WPPR_Name; ?></a></td>
        </tr>

        <tr>
            <th>Slug</th>
            <td><?php echo $args->slug ?? WPPR_Slug; ?></td>
        </tr>

        <tr>
            <th>Rating</th>
            <td><?php echo $args->rating ?? 'Not available'; ?>%</td>
        </tr>

        <tr>
            <th>Downloads</th>
            <td><?php echo $args->downloaded ?? 'Not available'; ?></td>
        </tr>

    </tbody>
</table>