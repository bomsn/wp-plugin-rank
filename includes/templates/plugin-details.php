<h3><b><?php echo WPPR_Name; ?> Details</b></h3>
<table class="wppr-plugin-details">
    <tbody>

        <tr>
            <th>Name</th>
            <td><a href="https://wordpress.org/plugins/<?php echo $args->slug; ?>" target="_blank"><?php echo $args->name; ?></a></td>
        </tr>

        <tr>
            <th>Slug</th>
            <td><?php echo $args->slug; ?></td>
        </tr>

        <tr>
            <th>Rating</th>
            <td><?php echo $args->rating; ?>%</td>
        </tr>

        <tr>
            <th>Downloads</th>
            <td><?php echo $args->downloaded; ?></td>
        </tr>

    </tbody>
</table>